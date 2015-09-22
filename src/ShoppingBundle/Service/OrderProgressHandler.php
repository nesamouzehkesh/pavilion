<?php

namespace ShoppingBundle\Service;

use AppBundle\Service\AppService;
use ShoppingBundle\Entity\Order;
use ShoppingBundle\Entity\Progress;
use ShoppingBundle\Entity\OrderProgress;
use UserBundle\Entity\User;

class OrderProgressHandler
{
    /**
     *
     * @var AppService $appService
     */
    protected $appService;
    
    /**
     * 
     * @param \AppBundle\Service\AppService $appService
     * @param type $parameters
     */
    public function __construct(
        AppService $appService, 
        $parameters
        ) 
    {
        $this->appService = $appService;
        $this->appService->setParametrs($parameters);
    }
    
    /**
     * 
     * @param Order $order
     * @param type $progress
     * @param User $user
     * @param type $description
     */
    public function handleProgress(Order $order, $progress, User $user = null, $description = '')
    {
        if ($progress instanceof Progress) {
            if ($progress->getType() === Progress::TYPE_STATIC) {
                $order->setProgressesStatus($progress->getId());
            }
        } else {
            $order->setProgressesStatus($progress);
            $progress = $this->getProgressReference($progress);
        }

        if (!$user instanceof User) {
            $user = $this->appService->getUser();
        }

        $orderProgress = new OrderProgress();
        $orderProgress->setUser($user);
        $orderProgress->setProgress($progress);
        $orderProgress->setDescription($description);
        
        // Set an "original expiry"
        $date = new \DateTime();        
        $orderProgress->setStartDate($date->getTimestamp());
        $orderProgress->setStatus(OrderProgress::STATUS_INPROGRESS);
        $orderProgress->setOrder($order);
        $this->appService->persistEntity($orderProgress);
        $order->addProgress($orderProgress);
        
        if (null !== $order->getId()) {
            OrderProgress::getRepository($this->appService->getEntityManager())
                ->finalizedAllProgresses($order);
        }
        
        return $orderProgress;
    }
    
    /**
     * 
     * @param type $progressId
     * @return type
     * @throws \Exception
     */
    private function getProgressReference($progressId)
    {
        if (!isset(Progress::$staticProgress[$progressId])) {
            throw new \Exception('Invalid order progress type is defined');
        }
        
        return $this->appService->getEntityManager()
            ->getReference('ShoppingBundle:Progress', $progressId);
    }
}