<?php

namespace ShoppingBundle\Service;

use AppBundle\Entity\Event;
use AppBundle\Service\AppService;
use AppBundle\Service\EventHandler;
use ShoppingBundle\Service\OrderProgressHandler;
use ShoppingBundle\Entity\OrderPayment;
use ShoppingBundle\Entity\Progress;
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
     * @var OrderProgressHandler $progressHandler
     */
    protected $progressHandler;

    /**
     * 
     * @param AppService $appService
     * @param EventHandler $eventHandler
     * @param OrderProgressHandler $progressHandler
     * @param type $parameters
     */
    public function __construct(
        AppService $appService, 
        EventHandler $eventHandler,
        OrderProgressHandler $progressHandler,
        $parameters
        ) 
    {
        $this->appService = $appService;
        $this->eventHandler = $eventHandler;
        $this->progressHandler = $progressHandler;
        $this->appService->setParametrs($parameters);
    }
    
    /**
     * 
     * @param Order $order
     * @param \ShoppingBundle\Service\User $user
     * @return type
     */
    public function handlePayment(Order $order, User $user)
    {
        $this->appService->transactionBegin();
        
        try {
            //TODO: do payment
            $paymentParams = array(
                'date' => '12/4/2015', 
                'value' => '39405', 
                'name' => 'saman shafigh',
                'type' => 1
                );

            $date = new \DateTime();
            $payment = $this->getPayment();
            $payment->setDate($date->getTimestamp());
            $payment->setOrder($order);
            $payment->setUser($user);
            $payment->setType(1);
            $payment->setContent(json_encode($paymentParams));
            $order->addPayment($payment);
            $this->appService->saveEntity($payment);
            
            // Handle the order progress
            $this->progressHandler->handleProgress($order, Progress::PROGRESS_PAID);
            // Handle the event related to this action
            $this->eventHandler->handleEvent($order, Event::TR_PAYMENT_ORDER);
            
            $this->appService->transactionCommit();

            return $payment;
        } catch (\Exception $ex) {
            $this->appService->transactionRollback();
            
            throw $ex;
        }
    }
    
    /**
     * Get an order
     * 
     * @param type $paymentId
     * @return OrderPayment
     * @throws \Exception
     */
    public function getPayment($paymentId = null)
    {
        if (null === $paymentId) {
            return new OrderPayment();
        }
        
        $payment = OrderPayment::getRepository($this->appService->getEntityManager())
            ->getPayment($paymentId);
        if (!$payment instanceof OrderPayment) {
            throw $this->appService->createVisibleHttpException('No payment has been found');
        }
        
        return $payment;
    }
}