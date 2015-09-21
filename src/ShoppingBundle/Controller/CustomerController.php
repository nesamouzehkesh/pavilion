<?php

namespace ShoppingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Library\Base\BaseController;
use ShoppingBundle\Form\CustomerType;
use UserBundle\Entity\User;
use UserBundle\Entity\Role;

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
                'title' => $this->trans('customer.profile'),
                'user' => $this->getUser(),
                )
            );
    }
    
    /**
     * 
     * @return type
     */
    public function registerConfirmationAction($userId)
    {
        $customer = $this->getCustomer($userId);
        
        return $this->render(
            '::web/customer/registerConfirmation.html.twig',
            array(
                'title' => $this->trans('customer.registerConfirmation'),
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
        // Get label object
        $customer = $this->getCustomer();

        // Generate Product Form
        $customerForm = $this->createForm(new CustomerType(), $customer);        
        $customerForm->handleRequest($request);
        // If form is submited and it is valid then add or update this $label
        if ($customerForm->isValid() and $this->customerFormDataIsValid($customerForm)) {
            // Get ObjectManager
            $em = $this->getDoctrine()->getManager();
            $userRole = Role::getRepository($em)->getUserRole();
            if (!$userRole instanceof Role) {
                throw $this->createVisibleHttpException('No user role has been found');
            }
            $customer->addRole($userRole);
            
            // Set this email for user email and username
            $email = $customerForm->get('email')->getData();
            $customer->setEmail($email);
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
     * Check if customer form data is valid
     * 
     * @param type $customerForm
     * @return boolean
     */
    private function customerFormDataIsValid($customerForm)
    {
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