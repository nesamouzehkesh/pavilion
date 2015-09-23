<?php

namespace ShoppingBundle\Service;

use AppBundle\Entity\Event;
use AppBundle\Service\AppService;
use AppBundle\Service\EventHandler;
use ShoppingBundle\Entity\Order;
use ShoppingBundle\Entity\Progress;
use ShoppingBundle\Service\OrderProgressHandler;
use UserBundle\Entity\User;

class OrderService
{
    /**
     * @var AppService $appService
     */
    protected $appService;
    
    /**
     * @var EventHandler $eventHandler
     */
    protected $eventHandler;
    
    /**
     * @var OrderProgressHandler $progressHandler
     */
    protected $progressHandler;

    /**
     * 
     * @param AppService $appService
     * @param EventHandler $eventHandler
     * @param OrderProgressHandler $progressHandler
     * @param type $parameters
     */
    public function __construct(
        AppService $appService, 
        EventHandler $eventHandler,
        OrderProgressHandler $progressHandler,
        $parameters
        ) 
    {
        $this->appService = $appService;
        $this->eventHandler = $eventHandler;
        $this->progressHandler = $progressHandler;
        $this->appService->setParametrs($parameters);
    }
    
    /**
     * 
     * @param Order $order
     * @param type $mediaService
     */
    public function updateCustomOrder(Order $order, $mediaService)
    {
        $this->appService->transactionBegin();
        
        try {
            $isNew = (null === $order->getId());
            $order->setType(Order::ORDER_TYPE_CUSTOM);
            $order->setUser($this->appService->getUser());
            $this->appService
                ->setMediaService($mediaService)
                ->saveMedia($order);

            // Handle the SUBMITTED progress order
            if ($isNew) {
                $this->progressHandler
                    ->handleProgress($order, Progress::PROGRESS_SUBMITTED);
            }
            
            // Handle the event related to this action
            $this->eventHandler->handleEvent(
                $order, 
                $isNew? Event::TR_ADD_ORDER : Event::TR_EDIT_ORDER
                );
            $this->appService->transactionCommit();
        } catch (\Exception $ex) {
            $this->appService->transactionRollback();
            
            throw $ex;
        }            
    }
    
    /**
     * 
     * @param User $user
     * @param type $params
     * @return type
     */
    public function getUserOrders(User $user, $params = array())
    {
        return $this->getOrders($user, $params, false);
    }
    
    /**
     * 
     * @param User $user
     * @param type $justQuery
     * @return type
     */
    public function getOrders(User $user = null, $params = array(), $justQuery = true)
    {
        return Order::getRepository($this->appService->getEntityManager())
            ->getOrders($user, $params, $justQuery);
    }
    
    /**
     * 
     * @param User $user
     * @param type $orderId
     * @return type
     */
    public function getUserOrder(User $user, $orderId = null)
    {
        return $this->getOrder($orderId, $user);
    }
    
    /**
     * Get an order
     * 
     * @param type $orderId
     * @return Order
     * @throws \Exception
     */
    public function getOrder($orderId = null, User $user = null)
    {
        if (null === $orderId) {
            return new Order();
        }
        
        $order = Order::getRepository($this->appService->getEntityManager())
            ->getOrder($orderId, $user);
        if (!$order instanceof Order) {
            throw $this->appService->createVisibleHttpException('No order has been found');
        }            
        
        return $order;
    }
}