<?php

namespace Library\Api\Payment;

use Library\Base\BaseApi;
use ShoppingBundle\Entity\Order;

/**
 * The AbstractPaymentApi class contains common methods for payment api
 */
abstract class AbstractPaymentApi extends BaseApi
{
    /**
     * shopping cart continer
     * 
     * @var type 
     */
    private $order = null;
    
    /**
     * 
     * @param Order $order
     * @return \Library\Api\Payment\AbstractPaymentApi
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
     * Remove all products from shopping cart
     */
    abstract public function create($param = array());
    
    /**
     * $paymentData
     */
    abstract public function execute($param = array());
}