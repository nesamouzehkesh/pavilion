<?php

namespace ShoppingBundle\Controller;

use Library\Base\BaseController;
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
        
        // Initializing Action parametrs related to PayPalPaymentApi
        $param = array(
            'returnUrl' => $this->generateUrl(
                'saman_shopping_order_payment_execute', 
                array('paymentId' => $payment->getId()),
                UrlGeneratorInterface::ABSOLUTE_URL
                ), 
            'cancelUrl' => $this->generateUrl(
                'saman_shopping_order_set_shipping',
                array('paymentId' => $payment->getId()),
                UrlGeneratorInterface::ABSOLUTE_URL
                )
            );
        
        // Creating a payment based on this $payment PayPalPaymentApi
        $paymentResponse = $this->getPaymentService()
            ->setPayment($payment)
            ->createPaymentRequest($param);
        
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
        $param = array('paymentData' => $paymentData, 'payerId' => $payerId);
        
        var_dump($param);
        
        // Get user payment
        $payment = $this->getShoppingService()
            ->getUserOrderPayment($user, $paymentId);
        
        // Execute user payment
        $this->getPaymentService()
            ->setPayment($payment)
            ->executePaymentRequest($param);
        
        // Finalizing user payment
        $this->getShoppingService()->finalizeOrderPayment($payment, $param);
            
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
        $payment = $this->getShoppingService()
            ->getUserOrderPayment($user, $paymentId);
        
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