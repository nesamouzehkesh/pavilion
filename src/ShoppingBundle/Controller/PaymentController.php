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
    public function paymentAction($orderId)
    {
        $order = $this->getShoppingService()->getUserOrder(
            $this->getUser(), 
            $orderId,
            true
            );

        $returnUrl = $this->generateUrl(
            'saman_shopping_order_payment_finalization', 
            array('orderId' => $orderId),
            UrlGeneratorInterface::ABSOLUTE_URL
            );
        $cancelUrl = $this->generateUrl(
            'saman_shopping_order_set_shipping_address',
            array('orderId' => $orderId),
            UrlGeneratorInterface::ABSOLUTE_URL
            );
        
        $paymentResponse = $this->getPaymentService()->paymentSubmission(
            $order,
            $returnUrl,
            $cancelUrl
            );
        $this->getSession()->set('paymentData', $paymentResponse->getPaypalData());
        
        return $this->redirect($paymentResponse->getApprovalUrl());
    }
    
    /**
     * 
     * @param type $orderId
     * @return type
     */
    public function paymentFinalizationAction($orderId)
    {
        $user = $this->getUser();
        $paymentData = $this->getSession()->get('paymentData');
        $payerId = $this->getGET('PayerID');

        $order = $this->getShoppingService()->getUserOrder(
            $this->getUser(), 
            $orderId
            );
        
        $payment = $this->getPaymentService()->paymentFinalization(
            $user, 
            $order,
            $payerId,
            $paymentData
            );
        
        return $this->redirectToRoute(
            'saman_shopping_order_payment_confirmation', 
            array('payerId' => $payment->getId())
            );
    }    
    
    /**
     * 
     * @param type $paymentId
     * @return type
     * @throws \Exception
     */
    public function paymentConfirmationAction($paymentId)
    {
        $user = $this->getUser();
        $payment = $this->getPaymentService()
            ->getUserPayment($user, $paymentId);
        
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
     * @return \ShoppingBundle\Service\PaymentService
     */
    private function getPaymentService()
    {
        return $this->getService('saman_shopping.payment');
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