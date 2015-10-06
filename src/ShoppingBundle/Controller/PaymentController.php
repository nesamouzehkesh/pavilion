<?php

namespace ShoppingBundle\Controller;

use Library\Base\BaseController;

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
        $order = $this->getShoppingService()->getUserOrder($user, $orderId);
        $payment = $this->getPaymentService()->handleUserPayment($user, $order);
        
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
        $user = $this->getUser();
        $payment = $this->getPaymentService()->getUserPayment($user, $paymentId);
        
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