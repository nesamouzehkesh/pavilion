<?php

namespace ShoppingBundle\Service;

use AppBundle\Service\AppService;
use ShoppingBundle\Entity\Order;
use ShoppingBundle\Entity\Progress;
use ShoppingBundle\Entity\OrderProgress;

class OrderService
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
     * @param type $mediaService
     */
    public function updateCustomOrder(Order $order, $mediaService)
    {
        if (null === $order->getId()) {
            $this->handleOrderProgress($order, Progress::PROGRESS_SUBMITTED);
        }
        
        // Get ObjectManager
        $order->setType(Order::ORDER_TYPE_CUSTOM);
        $order->setUser($this->appService->getUser());

        $this->getAppService()
            ->setMediaService($mediaService)
            ->saveMedia($order);
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
     * 
     * @param Order $order
     * @param type $progressId
     */
    public function handleOrderProgress(Order $order, $progressId)
    {
        $progress = $this->getProgressReference($progressId);
        $orderProgress = new OrderProgress();
        $orderProgress->setProgress($progress);
        
        // Set an "original expiry"
        $date = new \DateTime();        
        $orderProgress->setStartDate($date->getTimestamp());
        $orderProgress->setStatus(OrderProgress::STATUS_INPROGRESS);
        
        $order->addProgress($orderProgress);
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
        
        return $this->appService->getEntityManager()->getReference(
            'ShoppingBundle:Progress', 
            Progress::PROGRESS_SUBMITTED
            );
    }
}