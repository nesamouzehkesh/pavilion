<?php

namespace ShoppingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Library\Base\BaseController;
use ShoppingBundle\Form\CustomerType;
use UserBundle\Form\AddressType;
use UserBundle\Entity\User;
use UserBundle\Entity\Role;
use UserBundle\Entity\Address;

class CustomerController extends BaseController
{
    /**
     * Display customer profile
     * 
     * @return type
     */
    public function displayProfileAction()
    {
        return $this->render(
            '::web/customer/profile.html.twig',
            array(
                'user' => $this->getUser(),
                )
            );
    }
    
    /**
     * TODO: security issue 
     * @return type
     */
    public function registerConfirmationAction($userId)
    {
        $customer = $this->getCustomer($userId);
        
        return $this->render(
            '::web/customer/registerConfirmation.html.twig',
            array(
                'user' => $customer,
                )
            );
    }
    
    /**
     * Add new customer
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function registerAction(Request $request)
    {
        // Create new customer
        $customer = $this->getCustomer();

        // Generate Product Form
        $customerForm = $this->createForm(new CustomerType(), $customer);        
        $customerForm->handleRequest($request);
        // If form is submited and it is valid then add or update this $label
        if ($this->isCustomerFormDataValid($customerForm)) {
            // Get ObjectManager
            $em = $this->getDoctrine()->getManager();
            $userRole = Role::getRepository($em)->getUserRole();
            if (!$userRole instanceof Role) {
                throw $this->createVisibleHttpException('No user role has been found');
            }
            $customer->addRole($userRole);
            
            // Set this email for user email and username
            $email = $customerForm->get('email')->getData();
            $customer->setUsername($email);
            $em->persist($customer);
            $em->flush();

            return $this->redirectToRoute(
                'saman_shopping_customer_joinus_confirmation',
                array('userId' => $customer->getId())
                );
        }
        
        return $this->render(
            '::web/customer/register.html.twig', 
            array(
                'title' => $this->trans('word.createAccount'),
                'form' => $customerForm->createView(),
                )
            );
    }

    /**
     * Edit user profile info
     * 
     * @param Request $request
     * @param type $customerId
     * @return type
     * @throws type
     */
    public function editProfileAction(Request $request, $customerId)
    {
        try {
            // Get customer
            $customer = $this->getCustomer($customerId);
            if (!$customer instanceof User) {
                throw $this->createVisibleHttpException('No profile has been found');       
            }

            // Generate Customer edit profile Form
            $customerForm = $this->createForm(
                new CustomerType('editUser'), 
                $customer,
                array(
                    'action' => $request->getUri(),
                    'method' => 'post'
                    )
                );            
            $customerForm->handleRequest($request);
            // If form is submited and it is valid then add or update this $label
            if ($this->isCustomerFormDataValid($customerForm, 'editUser')) {
                // Get ObjectManager
                $em = $this->getDoctrine()->getManager();

                // Set this email for user email and username
                $password = $customerForm->get('newPassword')->getData();
                $customer->setPassword($password);

                $em->persist($customer);
                $em->flush();

                return $this->getJsonResponse(true);
            }

            $view = $this->renderView(
                '::web/customer/form/editProfile.html.twig', 
                array(
                    'form' => $customerForm->createView(),
                    )
                );
            return $this->getJsonResponse(true, null, $view);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse('Can not edit address', $ex);
        }
    }
    
    /**
     * 
     * @param Request $request
     * @param type $addressId
     * @return type
     * @throws type
     */
    public function addEditAddressAction(Request $request, $addressId)
    {
        try {
            // Get ObjectManager
            $em = $this->getDoctrine()->getManager();
            // Get Address object
            $address = Address::getRepository($em)
                ->findUserAddress($this->getUser(), $addressId);
            if (!$address instanceof Address) {
                throw $this->createVisibleHttpException('No address has been found');       
            }

            // Generate Product Form
            $addressForm = $this->createForm(
                new AddressType(), 
                $address,
                array(
                    'action' => $request->getUri(),
                    'method' => 'post'
                    )
                );            
            $addressForm->handleRequest($request);
            // If form is submited and it is valid then add or update this $label
            if ($addressForm->isValid()) {
                
                $em->persist($address);
                $em->flush();

                return $this->getJsonResponse(true);
            }
            
            $view = $this->renderView(
                '::web/customer/form/address.html.twig', 
                array(
                    'title' => $this->trans('word.createAccount'),
                    'form' => $addressForm->createView(),
                    )
                );
            
            return $this->getJsonResponse(true, null, $view);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse('Can not edit address', $ex);
        }
    }
    
    /**
     * Check if customer form data is valid
     * 
     * @param type $customerForm
     * @param type $type
     * @return boolean
     */
    private function isCustomerFormDataValid($customerForm, $type = null)
    {
        if (!$customerForm->isValid()) {
            return false;
        }
        
        if ('editUser' === $type) {
            // Get some extra form field that are not mapped to user object. 
            $password = $customerForm->get('password')->getData();
            if ($this->getUser()->checkPassword($password)) {
                $this->addFormError($customerForm->get('password'), 'Password is not right');
            }
            
            $newPassword = $customerForm->get('newPassword')->getData();
            $reNewPassword = $customerForm->get('reNewPassword')->getData();
            if ($newPassword !== $reNewPassword) {
                $this->addFormError($customerForm->get('reNewPassword'), 'Passwords are not same');
                
                return false;
            }
        } else {
            // Get ObjectManager
            $em = $this->getDoctrine()->getManager();
            
            // Get some extra form field that are not mapped to user object. 
            $email = $customerForm->get('email')->getData();
            $reEmail = $customerForm->get('reEmail')->getData();

            if ($email !== $reEmail) {
                $this->addFormError($customerForm->get('reEmail'), 'Emails are not same');
                
                return false;
            }

            if (User::getRepository($em)->hasUserByUsername($email)) {
                $this->addFormError($customerForm->get('email'), 'This emails is already used');
                
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Get an order
     * 
     * @param type $userId
     * @return Order
     * @throws \Exception
     */
    private function getCustomer($userId = null)
    {
        // Get ObjectManager
        $em = $this->getDoctrine()->getManager();        
        if (null === $userId) {
            // Get User object
            $customer = new User();
        } else {
            $customer = User::getRepository($em)->getUser($userId);
            if (!$customer instanceof User) {
                throw $this->createVisibleHttpException('No user has been found');
            }            
        }
        
        return $customer;
    }    
}