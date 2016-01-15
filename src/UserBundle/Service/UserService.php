<?php

namespace UserBundle\Service;

use Symfony\Component\Form\Form;
use AppBundle\Service\AppService;
use UserBundle\Entity\UserActivity;
use UserBundle\Entity\User;
use UserBundle\Entity\Role;

class UserService
{
    /**
     * @var AppService $appService
     */
    protected $appService;
    
    /**
     * 
     * @param AppService $appService
     */
    public function __construct(AppService $appService) 
    {
        $this->appService = $appService;
    }
        
    /**
     * 
     * @param Form $userForm
     * @return string|boolean
     */
    public function userFormIsValid(Form $userForm)
    {
        $em = $this->appService->getEntityManager();
        $user = $userForm->getData();
        $userName = $user->getUsername();
        
        // Check the username
        if (!preg_match("/^[a-zA-Z ]*$/", $userName)) {
            return "Only letters and white space allowed";
        }

        // Check the username
        if (!User::getRepository($em)->canUserUseUsername($user, $userName)) {
            return 'This username is already used';
        }

        if ($userForm->has('changePassword')) {
            if ($userForm->get('changePassword')->getData()) {
                // Check password and rePassword
                $password = $userForm->get('password')->getData();
                $rePassword = $userForm->get('rePassword')->getData();
                if ($password !== $rePassword) {
                    return 'Passwords are no matched';
                }
                // Check user current password
                if ($userForm->has('currentPassword')) {
                    $currentPassword = $userForm->get('currentPassword')->getData();
                    if ($user->getPassword() !== md5($currentPassword)) {
                        return 'Current passwords is not valid';
                    }
                }
                // Set this $password for user password
                $user->setPassword($password);
            }
        } else {
            // Check password and rePassword
            $password = $userForm->get('password')->getData();
            $rePassword = $userForm->get('rePassword')->getData();
            if ($password !== $rePassword) {
                return 'Passwords are no matched';
            }
            
            // Set this $password for user password
            $user->setPassword($password);
        }
        
        return true;
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
    public function getUser($userId = null, $isAdmin = false)
    {
        // If $userId is null it means that we want to create a new user object.
        // otherwise we find a user in DB based on this $userId
        if (null === $userId) {
            $user = new User;
            $role = $this->getRole($isAdmin? Role::ROLE_ADMIN : Role::ROLE_USER);
            $user->addRole($role);
            
            return $user;
        }
        
        // Get ObjectManager
        $em = $this->appService->getEntityManager();

        // Get User repository
        $user = User::getRepository($em)->find($userId);

        // Check if $user is found
        if (!$user instanceof User) {
            throw new \Exception('No user was found for id ' . $userId);
        }
        
        // Return user object
        return $user;
    }
    
    /**
     * Get user activities
     * 
     * @param User $user
     * @return type
     */
    public function getUserActivities(User $user)
    {
        // Get ObjectManager
        $em = $this->appService->getEntityManager();
        
        $userActivities = UserActivity::getRepository($em)->getUserActivities($user);
        foreach ($userActivities as $key => $userActivity) {
            $userActivities[$key]['title'] = UserActivity::getTitle($userActivity['type']);
        }
        
        return $userActivities;
    }
    
    /**
     * 
     * @param type $roleId
     * @return Role
     * @throws \Exception
     */
    public function getRole($roleId)
    {
        $em = $this->appService->getEntityManager();
        $role = Role::getRepository($em)->findOneBy(array('role' => $roleId));

        // Check if $user is found
        if (!$role instanceof Role) {
            throw new \Exception('No role was found for id ' . $roleId);
        }
        
        // Return user object
        return $role;
    }    
    
    /**
     * On successful login event
     * 
     * @param InteractiveLoginEvent $event
     */
    public function logUserActivity(User $user, $type)
    {
        $time = $this->appService->getTimestamp();
        
        $userActivity = new UserActivity;
        $userActivity->setTime($time);
        $userActivity->setType($type);
        $userActivity->setUser($user);
        $this->appService->saveEntity($userActivity);
        
        return $userActivity;
    }    
}