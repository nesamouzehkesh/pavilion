<?php

namespace UserBundle\Service;

use AppBundle\Service\AppService;
use UserBundle\Entity\UserActivity;
use UserBundle\Entity\User;

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
    public function getUser($userId = null)
    {
        // If $userId is null it means that we want to create a new user object.
        // otherwise we find a user in DB based on this $userId
        if (null === $userId) {
            return new User;
        }
        
        // Get ObjectManager
        $em = $this->appService->getEntityManager();

        // Get User repository
        $user = User::getRepository($em)->find($userId);

        // Check if $user is found
        if (!$user instanceof User) {
            throw new \Exception('No user found for id ' . $userId);
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