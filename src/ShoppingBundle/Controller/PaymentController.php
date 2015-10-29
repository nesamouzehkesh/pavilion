<?php

namespace ShoppingBundle\Controller;

use Library\Base\BaseController;
use Library\Api\Payment\PayPalPaymentApi;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends BaseController
{
    /**
     * 
     * @param type $orderId
     * @return type
     */
    public function createPaymentAction($orderId)
    {
        $user = $this->getUser();
        $order = $this->getShoppingService()->getUserOrder($user, $orderId, true);
        $payment = $this->getShoppingService()->createOrderPayment($order);

        return $this->redirectToRoute(
            'saman_shopping_order_payment_finalization', 
            array('paymentId' => $payment->getId())
            );
        
        // Initializing Action parametrs related to PayPalPaymentApi
        $actionParams = array(
            'returnUrl' => $this->generateUrl(
                'saman_shopping_order_payment_finalization', 
                array('paymentId' => $payment->getId()),
                UrlGeneratorInterface::ABSOLUTE_URL
                ), 
            'cancelUrl' => $this->generateUrl(
                'saman_shopping_order_set_shipping_address',
                array('paymentId' => $payment->getId()),
                UrlGeneratorInterface::ABSOLUTE_URL
                )
            );
        
        // Initializing PayPalPaymentApi
        $paymentApi = new PayPalPaymentApi(
            $this->getParameter('saman_payment.sandbox.paypal', true)
            );
        
        // Creating a payment based on this PayPalPaymentApi
        $paymentResponse = $this->getPaymentService()
            ->setPaymentApi($paymentApi)
            ->createPaymentRequest(
                $order,
                $actionParams
            );
        
        $this->getSession()->set('paymentData', $paymentResponse->getPaypalData());
        
        return $this->redirect($paymentResponse->getApprovalUrl());
    }
    
    /**
     * 
     * @param type $paymentId
     * @return type
     */
    public function executePaymentAction($paymentId)
    {
        $user = $this->getUser();
        $paymentData = $this->getSession()->get('paymentData');
        $payerId = $this->getGET('PayerID');

        $payment = $this->getPaymentService()->getPayment($paymentId);
        $order = $payment->getOrder();
        $this->getShoppingService()->loadOrder($order);
        
        $payPalParams = $this->getParameter('saman_payment.sandbox.paypal', true);
        $paymentApi = new PayPalPaymentApi($payPalParams);   
        
        $payerId = "7E7MGXCWTTKK2";
        $paymentData = array(
            "id" => "PAY-6RV70583SB702805EKEYSZ6Y",
            "create_time" => "2013-03-01T22:34:35Z",
            "update_time" => "2013-03-01T22:34:36Z",
            "state" => "created",
            "intent" => "sale"
            );
        
        $payment = $this->getPaymentService()
            ->setPaymentApi($paymentApi)
            ->executePaymentRequest(
                $user, 
                $order,
                array('paymentData' => $paymentData, 'payerId' => $payerId)
            );
        
        $this->getShoppingService()->finalizeOrderPayment(
            $order, 
            $payment
            );
            
        return $this->redirectToRoute(
            'saman_shopping_order_payment_confirmation', 
            array('paymentId' => $payment->getId())
            );
    }    
    
    /**
     * 
     * @param type $paymentId
     * @return type
     * @throws \Exception
     */
    public function confirmPaymentAction($paymentId)
    {
        $user = $this->getUser();
        $payment = $this->getPaymentService()
            ->getUserPayment($user, $paymentId, true);
        
        return $this->render(
            '::web/order/orderPaymentConfirmation.html.twig',
            array(
                'payment' => $payment,
                'orderConfig' => $this->getParameter('saman_shopping_order')
                )
            );        
    }    
    
    /**
     * Get Order service
     * 
     * @return \AppBundle\Service\PaymentService
     */
    private function getPaymentService()
    {
        return $this->getService('saman.payment');
    }
    
    /**
     * Get Order service
     * 
     * @return \ShoppingBundle\Service\ShoppingService
     */
    private function getShoppingService()
    {
        return $this->getService('saman_shopping.shopping');
    }
}