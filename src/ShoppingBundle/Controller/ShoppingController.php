<?php

namespace ShoppingBundle\Controller;

use Library\Base\BaseController;
use Symfony\Component\HttpFoundation\Request;
use ShoppingBundle\Entity\Order;
use ShoppingBundle\Form\OrderType;
use ShoppingBundle\Form\ShippingAddressType;
use UserBundle\Entity\Address;

class ShoppingController extends BaseController
{
    /**
     * 
     * @return type
     */
    public function displayShoppingListAction()
    {
        try {
            return $this->getOrderService()->displayShoppingList();
        } catch (Exception $ex) {
            return $this->getExceptionResponse('There is a problem in displaying your orders', $ex);
        }    
    }
    
    /**
     * 
     * @return type
     */
    public function displayOrdersAction()
    {
        try {
            $user = $this->getUser();
            $em = $this->getDoctrine()->getManager();
            $orders = Order::getRepository($em)->getUserOrders($user);

            return $this->render(
                '::web/order/orders.html.twig',
                array('orders' => $orders)
                );            
        } catch (Exception $ex) {
            return $this->getExceptionResponse('There is a problem in displaying your orders', $ex);
        }    
    }
    
    /**
     * 
     * @param type $orderId
     * @return type
     */
    public function displayOrderAction($orderId)
    {
        try {
            $order = $this->getOrder($orderId);

            return $this->render(
                '::web/order/order.html.twig',
                array(
                    'order' => $order,
                    'orderConfig' => $this->getParameter('saman_shopping_order')
                )
            );          
        } catch (Exception $ex) {
            return $this->getExceptionResponse('There is a problem in displaying your order', $ex);
        }    
    }
    
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function addEditOrderAction(Request $request, $orderId)
    {
        $order = $this->getOrder($orderId);
        $orderConfig = $this->getParameter('saman_shopping_order');
        // Generate Product Form
        $orderForm = $this->createForm(new OrderType($orderConfig), $order);        
        $orderForm->handleRequest($request);
        // If form is submited and it is valid then add or update this $label
        if ($orderForm->isValid()) {
            
            $mediaService = $this->getService('saman_media.media');
            $this->getOrderService()->updateCustomOrder($order, $mediaService);
            
            return $this->redirectToRoute(
                'saman_shopping_order_add_confirmation', 
                array('orderId' => $order->getId())
                );
        }

        return $this->render(
            '::web/order/addOrder.html.twig',
            array('form' => $orderForm->createView())
            );
    }
    
    /**
     * 
     * @param type $orderId
     * @return type
     * @throws \Exception
     */
    public function orderConfirmationAction($orderId)
    {
        $order = $this->getOrder($orderId);
        
        return $this->render(
            '::web/order/orderConfirmation.html.twig',
            array(
                'order' => $order,
                'orderConfig' => $this->getParameter('saman_shopping_order')
                )
            );        
    }
    
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function setShippingAddressAction(Request $request, $orderId)
    {
        $em = $this->getDoctrine()->getManager();
        $order = $this->getOrder($orderId);
        $user = $this->getUser();
        
        $orderShippingAddress = null;
        $orderBillingAddress = null;
        
        $primaryShippingAddress = $user->getPrimaryShippingAddress();
        $primaryBillingAddress = $user->getPrimaryBillingAddress();
        
        // Generate Product Form
        $form = $this->createForm(new ShippingAddressType($user));
        $form->handleRequest($request);
        // If form is submited and it is valid then add or update this $label
        if ($form->isValid()) {
            
            $billingSameAsShipping = false;
            if ($form->has('setNewShipping') and $form->get('setNewShipping')->getData()) {
                $shippingAddress = $form->get('shipping')->getData();
                $shippingAddress->setAddressType(Address::ADDRESS_TYPE_SHIPPING);
                $shippingAddress->setType(Address::TYPE_SECONDARY);
                //TODO: validate $shippingAddress
                if ($form->has('setShippingPrimary') and $form->get('setShippingPrimary')->getData()) {
                    $shippingAddress->setType(Address::TYPE_PRIMARY);
                    if ($primaryShippingAddress instanceof Address) {
                        $primaryShippingAddress->setType(Address::TYPE_SECONDARY);
                        $em->persist($primaryShippingAddress);
                    }
                }
                
                if ($form->has('billingSameAsShipping') and $form->get('billingSameAsShipping')->getData()) {
                    $billingSameAsShipping = true;
                    $shippingAddress->setAddressType(Address::ADDRESS_TYPE_BILLING_SHIPPING);
                    $orderBillingAddress = $shippingAddress;
                }
                $em->persist($shippingAddress);
                $orderShippingAddress = $shippingAddress;
            } else {
                $orderShippingAddress = $primaryShippingAddress;
            }
            
            
            if (!$billingSameAsShipping) {
                if ($form->has('setNewBilling') and $form->get('setNewBilling')->getData()) {
                    $billingAddress = $form->get('billing')->getData();
                    $billingAddress->setAddressType(Address::ADDRESS_TYPE_BILLING);
                    $billingAddress->setType(Address::TYPE_SECONDARY);
                    //TODO: validate $billingAddress
                    if ($form->has('setBillingPrimary') and $form->get('setBillingPrimary')->getData()) {
                        $billingAddress->setType(Address::TYPE_PRIMARY);
                        if ($primaryBillingAddress instanceof Address) {
                            $primaryBillingAddress->setType(Address::TYPE_SECONDARY);
                            $em->persist($primaryBillingAddress);
                        }
                    }
                    $em->persist($billingAddress);
                    $orderBillingAddress = $billingAddress;
                } else {
                    $orderBillingAddress = $primaryBillingAddress;
                }
            }
            
            if ($orderShippingAddress instanceof Address and $orderBillingAddress instanceof Address) {
                //$order->setB
                $em->flush();
                return $this->redirectToRoute(
                    'saman_shopping_order_add_confirmation', 
                    array('orderId' => $order->getId())
                    );
            }
        }

        return $this->render(
            '::web/order/setShippingAddress.html.twig',
            array(
                'primaryShippingAddress' => $primaryShippingAddress,
                'primaryBillingAddress' => $primaryBillingAddress,
                'form' => $form->createView(),
                'order' => $order,
                'orderConfig' => $this->getParameter('saman_shopping_order')                
                )
            );
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
    
    /**
     * Get an order
     * 
     * @param type $orderId
     * @return Order
     * @throws \Exception
     */
    private function getOrder($orderId = null)
    {
        // Get ObjectManager
        $em = $this->getDoctrine()->getManager();        
        if (null === $orderId) {
            // Get label object
            $order = new Order();
        } else {
            $order = Order::getRepository($em)->getOrder($orderId);
            if (!$order instanceof Order) {
                throw $this->createVisibleHttpException('No user has been found');
            }            
        }
        
        return $order;
    }
}