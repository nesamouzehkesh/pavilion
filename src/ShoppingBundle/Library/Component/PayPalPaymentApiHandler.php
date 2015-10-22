<?php

namespace ShoppingBundle\Library\Component;

use Guzzle\Http\Client;
use PayPalRestApiClient\Repository\AccessTokenRepository;
use PayPalRestApiClient\Service\PaymentService as PayPalPaymentService;
use PayPalRestApiClient\Builder\PaymentRequestBodyBuilder;
use PayPalRestApiClient\Builder\PaymentBuilder;
use PayPalRestApiClient\Model\Amount;
use PayPalRestApiClient\Model\Transaction;
use PayPalRestApiClient\Model\Payer;
use ShoppingBundle\Library\Serializer\PayPalOrderSerializer;

/**
 * The PayPalPaymentApi class contains methods for paypal payment api
 */
class PayPalPaymentApiHandler extends AbstractPaymentApiHandler
{
    const BASE_URL = 'https://api.paypal.com';
    const BASE_URL_SANDBOX = 'https://api.sandbox.paypal.com';
    
    /**
     *
     * @var type 
     */
    private $baseUrl;
    
    /**
     *
     * @var type 
     */
    private $clientId;
    
    /**
     *
     * @var type 
     */
    private $secret;

    /**
     * 
     * @param type $clientId
     * @param type $secret
     */
    public function __construct($clientId, $secret)
    {
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->baseUrl = self::BASE_URL_SANDBOX;
    }

    /**
     * Create Payment
     */
    public function create()
    {
        $client = new Client();
        $accessToken = $this->getAccessToken($client);
        $paymentService = $this->getPayPalPaymentService($client);
            
        $orderSerializer = new PayPalOrderSerializer();
        $amount = new Amount(
            $this->getOrder()->getCurrency(), 
            $this->getOrder()->callTotalPrice()
            );
        
        $payer = new Payer('paypal');
        $transaction = new Transaction(
            $amount, 
            'my transaction', 
            $orderSerializer->serialize($this->getOrder())
            );
        
        return $paymentService->create(
            $accessToken,
            $payer,
            array(
                'return_url' => $this->getParam('returnUrl'),
                'cancel_url' => $this->getParam('cancelUrl')
            ),
            array($transaction)
        );
    }
    
    /**
     * Execute the payment
     */
    public function execute()
    {
        $client = new Client();
        $accessToken = $this->getAccessToken($client);
        $paymentService = $this->getPayPalPaymentService($client);

        $paymentBuilder = new PaymentBuilder();
        $originalPayment = $paymentBuilder->build($this->getParam('paymentData'));
        $paymentResponse = $paymentService->execute(
            $accessToken, 
            $originalPayment, 
            $this->getParam('payerId')
            );
        
        if ('approved' !== $paymentResponse->getState()) {
            throw new \Exception('Payment failed');
        }
    }
    
    /**
     * 
     * @param Client $client
     * @return PayPalPaymentService
     */
    private function getPayPalPaymentService(Client $client)
    {
        return new PayPalPaymentService(
            $client, 
            new PaymentRequestBodyBuilder(), 
            $this->baseUrl
            );
    }
    
    /**
     * 
     * @param Client $client
     * @return type
     */
    private function getAccessToken(Client $client)
    {
        $repo = new AccessTokenRepository($client, $this->baseUrl);
        
        return $repo->getAccessToken($this->clientId, $this->secret);
    }
}