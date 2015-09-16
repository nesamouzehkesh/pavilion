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
        
        return $this->renderWebPage($content, $this->trans('shopping.webTitle'));
    }
    
    /**
     * Add new customer
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function addCustomerAction(Request $request)
    {
        // Get ObjectManager
        $em = $this->getDoctrine()->getManager();
        
        // Get label object
        $customer = new User();
        $userRole = $em->getReference('UserBundle:Role', 1);
        $customer->addRole($userRole);

        // Generate Product Form
        $customerForm = $this->createForm(
            new CustomerType(), 
            $customer,
            array(
                'action' => $request->getUri(),
                'method' => 'post'
                )
            );        
        
        $customerForm->handleRequest($request);
        // If form is submited and it is valid then add or update this $label
        if ($customerForm->isValid()) {
            // Get some extra form field that are not mapped to user object. 
            $email = $customerForm->get('email')->getData();
            $reEmail = $customerForm->get('reEmail')->getData();
            if ($email !== $reEmail) {
                return $this->getAppService()->getJsonResponse(
                    false, 
                    'Emails are no matched'
                    );
            }

            // Set this $password for user password
            $customer->setEmail($email);

            $em->persist($customer);
            $em->flush();
            
            return $this->getAppService()->getJsonResponse(true);
        }
        
        $content = $this->renderView(
            'ShoppingBundle:Customer:addCustomer.html.twig', 
            array(
                'form' => $customerForm->createView(),
                )
            );
        
        return $this->renderWebPage($content, $this->trans('shopping.webTitle'));
    }
}