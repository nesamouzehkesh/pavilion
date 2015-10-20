<?php

namespace ShoppingBundle\Service;

use Guzzle\Http\Client;
use PayPalRestApiClient\Repository\AccessTokenRepository;
use PayPalRestApiClient\Service\PaymentService as PayPalPaymentService;
use PayPalRestApiClient\Builder\PaymentRequestBodyBuilder;
use PayPalRestApiClient\Builder\PaymentBuilder;
use PayPalRestApiClient\Model\Amount;
use PayPalRestApiClient\Model\Transaction;
use PayPalRestApiClient\Model\Payer;

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
     * @param Order $order
     * @param type $returnUrl
     * @param type $cancelUrl
     * @return type
     * @throws \Exception
     */
    public function submitPayment(
        Order $order,
        $returnUrl,
        $cancelUrl
        )
    {
        /*
        $clientId = "AZIeWZv0pYj94Cffavh8itLs9sFrH0FRXmtpnWACDRObvPhSJkIgK4geRrTDTECGNA8-62I0t2Samh1M";
        $secret = "EDkbWqSYDtdUsc22PoHW-ebz7TkjTHRqpfB65T2Z6c6-AtyPjh7Lfc_I7h_lSPsUX0T3h95SCW4azwK9";
        $baseUrl = 'https://api.sandbox.paypal.com';
        */
        
        $clientId = "AadfWPDafi8zBcUyr3bGMWZtUJl5A6a-UjX0dg4HsE9uUD33Veydu6ozpS9ZyTnrJ8vmLRVMu5AW3Q6c";
        $secret = "ECek7G5nFmbFVIbE5SkKgkW0SaD1dU3sxmPKAMWTpPdS8vbvGlyhQTej9gXaP8K6k0Ny1xEKkkW-7pCR";
        $baseUrl = 'https://api.paypal.com';
        
        $client = new Client();
        $repo = new AccessTokenRepository($client, $baseUrl);        
        $accessToken = $repo->getAccessToken($clientId, $secret);

        $paymentService = new PayPalPaymentService(
            $client,
            new PaymentRequestBodyBuilder(),
            $baseUrl
        );        

        $itemList = array(
            'items' => array(
                array(
                    'quantity' => '1',
                    'name' => 'product name',
                    'price' => '1.01',
                    'currency' => 'USD',
                    'sku' => '1233456789',
                ),
            )
        );
        
        $amount = new Amount('USD', '1.01');
        $transaction = new Transaction(
            $amount, 
            'my transaction', 
            $itemList
            );
        $payer = new Payer('paypal');
        
        $payment = $paymentService->create(
            $accessToken,
            $payer,
            array(
                'return_url' => $returnUrl,
                'cancel_url' => $cancelUrl
            ),
            array($transaction)
        );
        
        return $payment;
    }
    
    public function finalizePayment(User $user, Order $order, $payerId, $paymentData)
    {
        $this->appService->transactionBegin();
        
        $clientId = "AadfWPDafi8zBcUyr3bGMWZtUJl5A6a-UjX0dg4HsE9uUD33Veydu6ozpS9ZyTnrJ8vmLRVMu5AW3Q6c";
        $secret = "ECek7G5nFmbFVIbE5SkKgkW0SaD1dU3sxmPKAMWTpPdS8vbvGlyhQTej9gXaP8K6k0Ny1xEKkkW-7pCR";
        $baseUrl = 'https://api.paypal.com';
        
        try {
            $client = new Client();
            $paymentService = new PayPalPaymentService(
                $client,
                new PaymentRequestBodyBuilder(),
                $baseUrl
            );

            $repo = new AccessTokenRepository($client, $baseUrl);        
            $accessToken = $repo->getAccessToken($clientId, $secret);
            
            $paymentBuilder = new PaymentBuilder();
            $originalPayment = $paymentBuilder->build($paymentData);
            $payment = $paymentService->execute($accessToken, $originalPayment, $payerId);
            if ('approved' !== $payment->getState()) {
                throw new \Exception('Payment failed');
            }
            
            $date = new \DateTime();
            $paymentDate = $date->getTimestamp();
            
            $paymentName = 'saman';
            $paymentType = 1;
            $paymentValue = 2342;
            if ($order->isProductOrder()) {
                $paymentValue = $this->shoppingService->finalizeOrderShoppingCartList($order);
            }

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