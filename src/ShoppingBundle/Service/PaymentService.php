<?php

namespace ShoppingBundle\Service;

use AppBundle\Service\AppService;
use AppBundle\Service\EventHandler;
use AppBundle\Entity\Event;
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
     * @param User $user
     * @param Order $order
     * @return type
     * @throws \Exception
     */
    public function handleUserPayment(User $user, Order $order)
    {
        $this->appService->transactionBegin();
        
        try {
            $date = new \DateTime();
            $paymentDate = $date->getTimestamp();
            $paymentName = 'saman';
            $paymentType = 1;
            
            $paymentValue = 2342;
            if ($order->isProductOrder()) {
                $paymentValue = $this->shoppingService->finalizeOrderShoppingCartList($order);
            }

            //TODO: do payment
            $paymentParams = array(
                'date' => $paymentDate, 
                'value' => $paymentValue, 
                'name' => $paymentName,
                'type' => $paymentType
                );

            $payment = $this->getPayment();
            $payment->setDate($paymentDate);
            $payment->setOrder($order);
            $payment->setUser($user);
            $payment->setType(1);
            $payment->setName($paymentName);
            $payment->setContent(json_encode($paymentParams));
            $payment->setValue($paymentValue);
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