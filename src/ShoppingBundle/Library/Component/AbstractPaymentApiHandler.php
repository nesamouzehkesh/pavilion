<?php

namespace ShoppingBundle\Library\Component;

use ShoppingBundle\Entity\Order;

/**
 * The AbstractPaymentApiHandler class contains common methods for payment api
 */
abstract class AbstractPaymentApiHandler
{
    /**
     * shopping cart continer
     * 
     * @var type 
     */
    private $order = null;
    
    /**
     * Some param required by each particuler action
     * 
     * @var type 
     */
    private $param = array();
    
    /**
     * 
     * @param Order $order
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
    }
    
    /**
     * 
     * @return Order
     */
    public function getOrder()
    {
        if (null === $this->order) {
            throw new \Exception('No order is set');
        }
        
        return $this->order;
    } 
    
    /**
     * 
     * @param type $param
     * @return \ShoppingBundle\Library\Component\AbstractPaymentApiHandler
     */
    public function setParam($param)
    {
        $this->param = $param;
        
        return $this;
    }
    
    /**
     * 
     * @param type $key
     * @return type
     * @throws \Exception
     */
    public function getParam($key)
    {
        if (!array_key_exists($key, $this->param)) {
            throw new \Exception(sprintf('Payment API handler needs [%s] parameter', $key));
        }
        
        return $this->param[$key];
    }
    
    /**
     * Remove all products from shopping cart
     */
    abstract public function create();
    
    /**
     * $paymentData
     */
    abstract public function execute();
}