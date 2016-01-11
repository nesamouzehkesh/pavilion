<?php

namespace AppBundle\Service;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use AppBundle\Service\AppService;
use UserBundle\Service\UserService;
use UserBundle\Entity\UserActivity;

class SecurityListener
{
    /**
     * @var AppService $appService
     */
    protected $appService;
    
    /**
     * @var UserService $userService
     */
    protected $userService;

    /**
     * 
     * @param AppService $appService
     */
    public function __construct(AppService $appService, UserService $userService) 
    {
        $this->appService = $appService;
        $this->userService = $userService;
    }
    
    /**
     * On successful login event
     * 
     * @param InteractiveLoginEvent $event
     */
    public function onSuccessfulLoginEvent(InteractiveLoginEvent $event)
    {
        $user = $this->appService->getUser();
        $this->userService->logUserActivity($user, UserActivity::ACTIVITY_LOGIN);
    }    
}