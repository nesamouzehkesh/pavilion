<?php

namespace UserBundle\Controller;

use Library\Base\BaseController;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use UserBundle\Form\UserType;

class UserController extends BaseController
{
    public function myprofileAction()
    {
        $user = $this->getAppService()->getUser();
        
        return $this->render(
            'UserBundle:User:myprofile.html.twig', 
            array('user' => $user)
            );
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
            'UserBundle:User:users.html.twig',
            array(
                'usersPagination' => $usersPagination
                )
            );
    }
    
    /**
     * Display a user
     * 
     * @param type $userId
     * @return type
     */
    public function displayUserAction($userId)
    {
        // Get ObjectManager
        $user = $this->getUserService()->getUser($userId);
        $userActivities = $this->getUserService()->getUserActivities($user);
        
        $view = $this->renderView(
            'UserBundle:User:user.html.twig',
            array(
                'user' => $user,
                'userActivities' => $userActivities
                )
            );

        return $this->getAppService()->getJsonResponse(true, null, $view);        
    }
    
    /**
     * Display and handel add edit user action
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function addEditUserAction(Request $request, $userId = null)
    {
        try {
            // Get user object
            $user = $this->getUserService()->getUser($userId);

            // Generate User Form
            $userForm = $this->createForm(
                new UserType(), 
                $user,
                array(
                    'action' => $request->getUri(),
                    'method' => 'post'
                    )
                );

            $userForm->handleRequest($request);
            // If form is submited and it is valid then add or update this $user
            if ($userForm->isValid()) {

                // Get some extra form field that are not mapped to user object. 
                $password = $userForm->get('password')->getData();
                $rePassword = $userForm->get('rePassword')->getData();
                if ($password !== $rePassword) {
                    return $this->getAppService()->getJsonResponse(
                        false, 
                        'Passwords are no matched'
                        );
                }

                // Set this $password for user password
                $user->setPassword($password);

                // Get ObjectManager
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                return $this->getAppService()->getJsonResponse(true);
            }

            $view = $this->renderView(
                'UserBundle:User:addEditUser.html.twig', 
                array(
                    'form' => $userForm->createView(),
                    )
                );

            return $this->getAppService()->getJsonResponse(true, null, $view);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse('Can not add or edit user', $ex);
        }         
    }
    
    /**
     * Delete a user
     * 
     * @param type $userId
     * @return type
     */
    public function deleteUserAction($userId)
    {
        try {
            // Get user
            $user = $this->getUserService()->getUser($userId);

            // Get ObjectManager
            $em = $this->getDoctrine()->getManager();
            // Remove user and flush ObjectManager. Note: if this $user is used
            // running the following code will throw an exception. Before delete this 
            // object we need to be sure that it is not in any other places.
            $em->remove($user);
            $em->flush();

            return $this->getAppService()->getJsonResponse(true);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'alert.error.canNotDeleteItem', 
                $ex
                );
        }        
    }
    
    /**
     * 
     * @return \UserBundle\Service\UserService
     */
    private function getUserService()
    {
        return $this->get('saman.user_user');
    }
}
