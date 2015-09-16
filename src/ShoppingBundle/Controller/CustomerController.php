<?php

namespace ShoppingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Library\Base\BaseController;
use ShoppingBundle\Form\CustomerType;
use UserBundle\Entity\User;

class CustomerController extends BaseController
{
    /**
     * Display customer profile
     * 
     * @return type
     */
    public function displayProfileAction()
    {
        $content = $this->renderView(
            'ShoppingBundle:Customer:profile.html.twig', 
            array(
                'user' => $this->getUser(),
                )
            );
        
        return $this->render(
            '::web/content.html.twig',
            array(
                'title' => $this->trans('shopping.webTitle'),
                'content' => $content
                )
            );
    }
    
    public function joinusConfirmationAction()
    {
        $content = $this->renderView(
            '::web/modules/joinUsConfirmation.html.twig',
            array()
            );
        
        return $this->render(
            '::web/content.html.twig',
            array(
                'title' => $this->trans('shopping.webTitle'),
                'content' => $content
                )
            );        
    }
    
    /**
     * Add new customer
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function joinusAction(Request $request)
    {
        // Get label object
        $customer = new User();

        // Generate Product Form
        $customerForm = $this->createForm(new CustomerType(), $customer);        
        $customerForm->handleRequest($request);
        // If form is submited and it is valid then add or update this $label
        if ($customerForm->isValid() and $this->customerFormDataIsValid($customerForm)) {
            // Get ObjectManager
            $em = $this->getDoctrine()->getManager();
            $userRole = $em->getReference('UserBundle:Role', 1);
            $customer->addRole($userRole);
            
            // Set this email for user email and username
            $email = $customerForm->get('email')->getData();
            $customer->setEmail($email);
            $customer->setUsername($email);
            $em->persist($customer);
            $em->flush();

            return $this->redirectToRoute('saman_shopping_customer_joinus_confirmation');
        }
        
        $content = $this->renderView(
            'ShoppingBundle:Customer:joinus.html.twig', 
            array(
                'form' => $customerForm->createView(),
                )
            );
        
        return $this->render(
            '::web/content.html.twig',
            array(
                'title' => $this->trans('shopping.webTitle'),
                'content' => $content
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
}