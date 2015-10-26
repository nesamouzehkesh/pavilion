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
class PayPalPaymentApi extends AbstractPaymentApi
{
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
    public function __construct($params)
    {
        $this->clientId = $this->getParam('clientId', $params);
        $this->secret = $this->getParam('secret', $params);
        $this->baseUrl = $this->getParam('baseUrl', $params);
    }

    /**
     * Create Payment
     * 
     * @param type $param Action parameters
     * @return type
     */
    public function create($param = array())
    {
        $client = new Client();
        $accessToken = $this->getAccessToken($client);
        $paymentService = $this->getPayPalPaymentService($client);
            
        $orderSerializer = new PayPalOrderSerializer();
        $amount = new Amount(
            $this->getOrder()->getCurrency(), 
            (string) $this->getOrder()->callTotalPrice()
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
                'return_url' => $this->getParam('returnUrl', $param),
                'cancel_url' => $this->getParam('cancelUrl', $param)
            ),
            array($transaction)
        );
    }
    
    /**
     * Execute the payment
     * 
     * @param type $param
     * @return type
     * @throws \Exception
     */
    public function execute($param = array())
    {
        $client = new Client();
        $accessToken = $this->getAccessToken($client);
        $paymentService = $this->getPayPalPaymentService($client);
        $paymentData = $this->getParam('paymentData', $param);
        
        $paymentBuilder = new PaymentBuilder();
        $originalPayment = $paymentBuilder->build($paymentData);
        $paymentResponse = $paymentService->execute(
            $accessToken, 
            $originalPayment, 
            $this->getParam('payerId')
            );
        
        if ('approved' !== $paymentResponse->getState()) {
            throw new \Exception('Payment failed');
        }
        
        return $paymentResponse;
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