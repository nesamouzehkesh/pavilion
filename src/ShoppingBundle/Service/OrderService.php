<?php

namespace ShoppingBundle\Service;

use AppBundle\Entity\Event;
use AppBundle\Service\AppService;
use AppBundle\Service\EventHandler;
use ShoppingBundle\Entity\Order;
use ShoppingBundle\Entity\Progress;
use ShoppingBundle\Service\OrderProgressHandler;

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
     * Display page
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param type $pageUrl
     * @return type
     */
    public function displayOrders()
    {
        $user = $this->appService->getUser();
        $orders = Order::getRepository($this->appService->getEntityManager())
            ->getUserOrders($user);
        
        $content = $this->appService->renderView(
            'ShoppingBundle:Order:index.html.twig',
            array('orders' => $orders)
            );
        
        return $this->displayPage($content);
    }
    
    /**
     * Get an order
     * 
     * @param type $orderId
     * @return Order
     * @throws \Exception
     */
    public function getOrder($orderId = null)
    {
        if (null === $orderId) {
            return new Order();
        }
        
        $order = Order::getRepository($this->appService->getEntityManager())
            ->getOrder($orderId);
        if (!$order instanceof Order) {
            throw $this->appService->createVisibleHttpException('No user has been found');
        }            
        
        return $order;
    }
}