<?php

namespace ShoppingBundle\Service;

use AppBundle\Service\AppService;
use ShoppingBundle\Entity\Order;

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
}