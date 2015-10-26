<?php

namespace ShoppingBundle\Library\Component;

use ShoppingBundle\Entity\Order;

/**
 * The AbstractPaymentApi class contains common methods for payment api
 */
abstract class AbstractPaymentApi
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
     * @return \ShoppingBundle\Library\Component\AbstractPaymentApi
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
        
        return $this;
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
     * @return \ShoppingBundle\Library\Component\AbstractPaymentApi
     */
    public function setParam($param)
    {
        $this->param = $param;
        
        return $this;
    }
    
    /**
     * 
     * @param string $key
     * @param array $externalParam
     * @return mix
     * @throws \Exception
     */
    public function getParam($key, $externalParam = null)
    {
        $param = (null === $externalParam)? $this->param : $externalParam;
        if (!array_key_exists($key, $param)) {
            throw new \Exception(sprintf('Payment API handler needs [%s] parameter', $key));
        }
        
        return $param[$key];
    }
    
    /**
     * Remove all products from shopping cart
     */
    abstract public function create($param = array());
    
    /**
     * $paymentData
     */
    abstract public function execute($param = array());
}