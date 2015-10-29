<?php

namespace AppBundle\Service;

use Library\Api\Payment\AbstractPaymentApi;
use AppBundle\Service\AppService;
use AppBundle\Service\EventHandler;
use ShoppingBundle\Entity\OrderPayment;
use ShoppingBundle\Entity\Order;
use UserBundle\Entity\User;

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
     * 
     * @param AppService $appService
     * @param EventHandler $eventHandler
     * @param type $parameters
     */
    public function __construct(
        AppService $appService, 
        EventHandler $eventHandler,
        $parameters = array()
        ) 
    {
        $this->appService = $appService;
        $this->eventHandler = $eventHandler;
        $this->appService->setParametrs($parameters);
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
            throw new \Exception('No payment api is set to this service');
        }
        
        return $this->paymentApi;
    }

    /**
     * 
     * @param Order $order
     * @param type $param
     */
    public function createPaymentRequest(Order $order, $param = array())
    {
        return $this->getPaymentApi()->setOrder($order)->create($param);
    }
    
    /**
     * 
     * @param User $user
     * @param Order $order
     * @param type $param
     * @return type
     * @throws \Exception
     */
    public function executePaymentRequest(User $user, Order $order, $param = array())
    {
        $this->appService->transactionBegin();
        
        try {
            //$this->getPaymentApi()->setOrder($order)->execute($param);
            
            $payment = $this->getPayment();
            $payment->setOrder($order);
            $payment->setUser($user);
            $payment->setType(1);
            $payment->setName($user->getName());
            $payment->setContent(json_encode($param));
            $payment->setValue($order->getTotalPrice());
            
            $this->appService->persistEntity($payment);
            $this->appService->flushEntityManager();
            $this->appService->transactionCommit();

            return $payment;
        } catch (\Exception $ex) {
            $this->appService->transactionRollback();
            
            throw $ex;
        }
    }
    
    /**
     * Get a user payment
     * @scope user
     * 
     * @param type $paymentId
     * @return OrderPayment
     * @throws \Exception
     */
    public function getUserPayment(User $user, $paymentId = null)
    {
        return $this->getPayment($paymentId, $user);
    }
    
    /**
     * Get a payment
     * @scope admin
     * 
     * @param type $paymentId
     * @return OrderPayment
     * @throws \Exception
     */
    public function getPayment($paymentId = null, User $user = null)
    {
        if (null === $paymentId) {
            $payment = new OrderPayment();
            $payment->setStatus(OrderPayment::STATUS_CREATED);
        } else {
            $payment = OrderPayment::getRepository($this->appService->getEntityManager())
                ->getPayment($paymentId, $user);
            if (!$payment instanceof OrderPayment) {
                throw $this->appService->createVisibleHttpException('No payment has been found');
            }
        }
        
        return $payment;
    }
}