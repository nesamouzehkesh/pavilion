<?php

namespace Library\Api\Payment;

use Library\Base\BaseApi;

/**
 * The AbstractPaymentApi class contains common methods for payment api
 */
abstract class AbstractPaymentApi extends BaseApi
{
    /**
     * User payment
     * 
     * @var PaymentEntityInterface  $payment
     */
    private $payment = null;
    
    /**
     * 
     * @param \Library\Api\Payment\PaymentEntityInterface $payment
     * @return \Library\Api\Payment\AbstractPaymentApi
     */
    public function setPayment(PaymentEntityInterface $payment)
    {
        $this->payment = $payment;
        
        return $this;
    }
    
    /**
     * 
     * @return PaymentEntityInterface
     * @throws \Exception
     */
    public function getPayment()
    {
        if (null === $this->payment) {
            throw new \Exception('No payment is set');
        }
        
        return $this->payment;
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