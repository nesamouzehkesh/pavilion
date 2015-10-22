<?php

namespace ShoppingBundle\Library\Component;

/**
 * The PayPalPaymentApi class contains methods for paypal payment api
 */
class PaymentApi
{
    /**
     *
     * @var AbstractPaymentApiHandler payment api handler
     */
    private $paymentApiHandler;
    
    /**
     * 
     * @param \ShoppingBundle\Library\Component\AbstractShoppingCartModifier $paymentApiHandler
     */
    public function __construct(AbstractPaymentApiHandler $paymentApiHandler, $order)
    {
        $paymentApiHandler->setOrder($order);
        $this->paymentApiHandler = $paymentApiHandler;
    }    

    /**
     * 
     * @param type $param
     */
    public function create($param = array())
    {
        $this->paymentApiHandler->setParam($param);
        $this->paymentApiHandler->create();
    }
    
    /**
     * 
     * @param type $param
     */
    public function execute($param = array())
    {
        $this->paymentApiHandler->setParam($param);
        $this->paymentApiHandler->execute();
    }
}