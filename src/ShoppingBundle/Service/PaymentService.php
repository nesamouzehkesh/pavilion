<?php

namespace ShoppingBundle\Service;

use AppBundle\Service\AppService;
use AppBundle\Service\EventHandler;
use AppBundle\Entity\Event;
use ShoppingBundle\Library\Component\AbstractPaymentApi;
use ShoppingBundle\Service\ShoppingService;
use ShoppingBundle\Service\OrderProgressHandler;
use ShoppingBundle\Entity\Progress;
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
     *
     * @var ShoppingService $shoppingService
     */
    protected $shoppingService;

    /**
     * @var EventHandler $eventHandler
     */
    protected $eventHandler;
    
    /**
     * @var OrderProgressHandler $progressHandler
     */
    protected $progressHandler;
    
    /**
     * @var AbstractPaymentApi $paymentApi
     */
    protected $paymentApi;
    
    /**
     * 
     * @param AppService $appService
     * @param EventHandler $eventHandler
     * @param OrderProgressHandler $progressHandler
     * @param type $parameters
     */
    public function __construct(
        AppService $appService, 
        ShoppingService $shoppingService,
        EventHandler $eventHandler,
        OrderProgressHandler $progressHandler,
        $parameters
        ) 
    {
        $this->appService = $appService;
        $this->shoppingService = $shoppingService;
        $this->eventHandler = $eventHandler;
        $this->progressHandler = $progressHandler;
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
    public function createPayment(Order $order, $param = array())
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
    public function executePayment(User $user, Order $order, $param = array())
    {
        $this->appService->transactionBegin();
        
        try {
            //$this->getPaymentApi()->setOrder($order)->execute($param);
            
            $order->setIsPaid(true);
            if ($order->isProductOrder()) {
                $finalizedOrderItems = $this->shoppingService->finalizeShoppingCart($order);
                $order->setContent($finalizedOrderItems);               
                $order->setTotalPrice($order->callTotalPrice());
            }
            
            $payment = $this->getPayment();
            $payment->setOrder($order);
            $payment->setUser($user);
            $payment->setType(1);
            $payment->setName($user->getName());
            $payment->setContent(json_encode($param));
            $payment->setValue($order->getTotalPrice());
            $order->addPayment($payment);
            
            $this->appService->persistEntity($order);
            $this->appService->persistEntity($payment);
            $this->appService->flushEntityManager();
            $this->appService->transactionCommit();

            // Handle the order progress
            $this->progressHandler->handleProgress($order, Progress::PROGRESS_PAID);
            // Handle the event related to this action
            $this->eventHandler->handleEvent($order, Event::TR_PAYMENT_ORDER);            

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
            return new OrderPayment();
        }
        
        $payment = OrderPayment::getRepository($this->appService->getEntityManager())
            ->getPayment($paymentId, $user);
        if (!$payment instanceof OrderPayment) {
            throw $this->appService->createVisibleHttpException('No payment has been found');
        }
        
        return $payment;
    }
}