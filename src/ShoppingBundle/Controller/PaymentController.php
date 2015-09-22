<?php

namespace ShoppingBundle\Controller;

use Library\Base\BaseController;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends BaseController
{
    /**
     * 
     * @param type $orderId
     * @return type
     */
    public function orderPaymentAction($orderId)
    {
        $user = $this->getUser();
        $order = $this->getOrderService()->getOrder($orderId);
        $payment = $this->getPaymentService()->handlePayment($order, $user);
        
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
    public function orderPaymentConfirmationAction($paymentId)
    {
        $payment = $this->getPaymentService()->getPayment($paymentId);
        
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
     * @return \ShoppingBundle\Service\OrderService
     */
    private function getOrderService()
    {
        return $this->getService('saman_shopping.order');
    }
}