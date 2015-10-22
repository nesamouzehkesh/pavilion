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
use ShoppingBundle\Library\Component\PayPalPaymentApiHandler;
use ShoppingBundle\Library\Component\PaymentApi;

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
     * @var AbstractPaymentApiHandler $paymentApiHandler
     */
    protected $paymentApiHandler;
    
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
        $parameters_sandbox = array(
            'clientId' => "AZIeWZv0pYj94Cffavh8itLs9sFrH0FRXmtpnWACDRObvPhSJkIgK4geRrTDTECGNA8-62I0t2Samh1M",
            'secret' => "EDkbWqSYDtdUsc22PoHW-ebz7TkjTHRqpfB65T2Z6c6-AtyPjh7Lfc_I7h_lSPsUX0T3h95SCW4azwK9"
        );
        
        $parameters = array(
            'clientId' => "AadfWPDafi8zBcUyr3bGMWZtUJl5A6a-UjX0dg4HsE9uUD33Veydu6ozpS9ZyTnrJ8vmLRVMu5AW3Q6c",
            'secret' => "ECek7G5nFmbFVIbE5SkKgkW0SaD1dU3sxmPKAMWTpPdS8vbvGlyhQTej9gXaP8K6k0Ny1xEKkkW-7pCR"
        );

        $this->appService = $appService;
        $this->shoppingService = $shoppingService;
        $this->eventHandler = $eventHandler;
        $this->progressHandler = $progressHandler;
        $this->appService->setParametrs($parameters_sandbox);
        
        $this->paymentApiHandler = new PayPalPaymentApiHandler(
            $this->appService->getParameter('clientId'),
            $this->appService->getParameter('secret')
            );        
    }
    
    /**
     * 
     * @param Order $order
     * @param type $returnUrl
     * @param type $cancelUrl
     * @return type
     */
    public function paymentSubmission(Order $order, $returnUrl, $cancelUrl)
    {
        $paymentApi = new PaymentApi($this->paymentApiHandler, $order);
        $param = array('returnUrl' => $returnUrl, 'cancelUrl' => $cancelUrl);
        
        return $paymentApi->create($param);
    }
    
    /**
     * 
     * @param User $user
     * @param Order $order
     * @param type $payerId
     * @param type $paymentData
     * @return type
     * @throws \Exception
     */
    public function paymentFinalization(User $user, Order $order, $payerId, $paymentData)
    {
        $this->appService->transactionBegin();
        
        try {
            $paymentApi = new PaymentApi($this->paymentApiHandler, $order);
            $param = array('paymentData' => $paymentData, 'payerId' => $payerId);
            $paymentApi->execute($param);
            
            if ($order->isProductOrder()) {
                $order->setTotalPrice($order->callTotalPrice());
                $this->shoppingService->finalizeOrderShoppingCartList($order);
            }

            $payment = $this->getPayment();
            $payment->setOrder($order);
            $payment->setUser($user);
            $payment->setType(1);
            $payment->setName($user->getName());
            $payment->setContent(json_encode($paymentData));
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