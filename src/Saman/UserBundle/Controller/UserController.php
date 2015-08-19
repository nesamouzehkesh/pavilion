<?php

namespace Saman\UserBundle\Controller;

use Saman\Library\Base\BaseController;
use Symfony\Component\HttpFoundation\Request;
use Saman\UserBundle\Entity\User;

class UserController extends BaseController
{
    public function myprofileAction($name)
    {
        return $this->render('SamanUserBundle:User:myprofile.html.twig', array('name' => $name));
    }
    
    /**
     * Display all users in the user main page
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function displayUsersAction(Request $request)
    {
        // Get ObjectManager
        $em = $this->getDoctrine()->getManager();
        
        // Get all Users
        $users = User::getRepository($em)->findAllUsers();

        // Get pagination
        $usersPagination = $this->getAppService()
            ->paginate($request, $users);
        
        // Render and return the view
        return $this->render(
            'SamanUserBundle:User:users.html.twig',
            array(
                'usersPagination' => $usersPagination
                )
            );
    }
    
    /**
     * Display and handel add edit user action
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function addEditUserAction(Request $request, $userId = null)
    {
        // Get user object
        $user = $this->getUser($userId);
        
        // Create a form for this $user
        $form = $this->createFormBuilder($user)
            ->add('title', 'text')
            ->add('description', 'textarea')
            ->add('save', 'submit', array('user' => 'Add User'))
            ->getForm();
        
        $form->handleRequest($request);
        // If form is submited and it is valid then add or update this $user
        if ($form->isValid()) {
            // Get ObjectManager
            $em = $this->getDoctrine()->getManager();
            
            $em->persist($user);
            $em->flush();
            
            // Redirect to user main page. First we create the url to user main page
            // from its rout 'saman_admin_user_home' then we use redirect function to
            // redirect to this $url
            $url = $this->generateUrl('saman_admin_user_home');
            return $this->redirect($url, 301);            
        }
        
        return $this->render(
            'SamanUserBundle:User:addEditUser.html.twig', 
            array(
                'form' => $form->createView(),
                )
            );
    }
    
    /**
     * Delete a user
     * 
     * @param type $userId
     * @return type
     */
    public function deleteUserAction($userId)
    {
        // Get user
        $user = $this->getUser($userId);
        
        // Get ObjectManager
        $em = $this->getDoctrine()->getManager();
        // Remove user and flush ObjectManager. Note: if this $user is used
        // running the following code will throw an exception. Before delete this 
        // object we need to be sure that it is not in any other places.
        $em->remove($user);
        $em->flush();
        
        // Redirect to user main page. First we create the url to user main page
        // from its rout 'saman_admin_user_home' then we use redirect function to
        // redirect to this $url
        $url = $this->generateUrl('saman_admin_user_home');
        return $this->redirect($url, 301);
    }
    
    /**
     * Get a user based on $userId or create a new one if $userId is null
     * 
     * In a good practice development we should not put these kind of functions 
     * in controller, we can create a user service to performance these kind of 
     * actions
     * 
     * @param type $userId
     * @return User
     * @throws NotFoundHttpException
     */
    private function getUserE($userId = null)
    {
        // If $userId is null it means that we want to create a new user object.
        // otherwise we find a user in DB based on this $userId
        if (null === $userId) {
            $user = new User();
        } else {
            // Get ObjectManager
            $em = $this->getDoctrine()->getManager();
            
            // Get User repository
            $user = $em->getRepository('SamanUserBundle:User')->find($userId);
            
            // Check if $user is found
            if (!$user) {
                throw $this->createNotFoundException('No user found for id ' . $userId);
            }
        }
        
        // Return user object
        return $user;
    }
}
