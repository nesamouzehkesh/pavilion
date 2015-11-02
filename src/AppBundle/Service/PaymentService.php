<?php

namespace AppBundle\Service;

use Library\Api\Payment\PaymentEntityInterface;
use Library\Api\Payment\AbstractPaymentApi;
use Library\Api\Payment\PayPalPaymentApi;
use AppBundle\Service\AppService;
use AppBundle\Service\EventHandler;

class PaymentService
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
     * @var AbstractPaymentApi $paymentApi
     */
    protected $paymentApi;

    /**
     * @var PaymentEntityInterface $payment
     */
    protected $payment;
    
    /**
     * 
     * @param AppService $appService
     * @param EventHandler $eventHandler
     * @param type $parameters
     */
    public function __construct(
        AppService $appService, 
        EventHandler $eventHandler,
        $parameters
        ) 
    {
        $this->appService = $appService;
        $this->eventHandler = $eventHandler;
        $this->appService->setParametrs($parameters);
    }
    
    /**
     * 
     * @param PaymentEntityInterface $payment
     * @return \AppBundle\Service\PaymentService
     */
    public function setPayment(PaymentEntityInterface $payment)
    {
        $this->payment = $payment;
        
        return $this;
    }
    
    /**
     * 
     * @param AbstractPaymentApi $paymentApi
     * @return \ShoppingBundle\Service\PaymentService
     */
    public function setPaymentApi(AbstractPaymentApi $paymentApi)
    {
        $this->paymentApi = $paymentApi;
        
        return $this;
    }
    
    /**
     * 
     * @return AbstractPaymentApiHandler
     * @throws \Exception
     */
    public function getPaymentApi()
    {
        if (!$this->paymentApi instanceof AbstractPaymentApi) {
            // Initializing default PayPalPaymentApi
            $this->paymentApi = new PayPalPaymentApi(
                $this->appService->getParameter('sandbox')['paypal']
                );            
        }
        
        if (!$this->payment instanceof PaymentEntityInterface) {
            throw new \Exception('You should set user payment to this service');
        }
        
        return $this->paymentApi->setPayment($this->payment);
    }

    /**
     * 
     * @param type $param
     * @return type
     */
    public function createPaymentRequest($param = array())
    {
        return $this->getPaymentApi()->create($param);
    }
    
    /**
     * 
     * @param type $param
     * @return type
     */
    public function executePaymentRequest($param = array())
    {
        return $this->getPaymentApi()->execute($param);
    }
}