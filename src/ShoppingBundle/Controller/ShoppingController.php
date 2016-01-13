<?php

namespace ShoppingBundle\Controller;

use Library\Base\BaseController;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\Address;
use ShoppingBundle\Form\OrderType;
use ShoppingBundle\Form\OrderShippingType;
use ShoppingBundle\Entity\Order;

class ShoppingController extends BaseController
{
    /**
     * 
     * @return type
     */
    public function displayOrdersAction()
    {
        try {
            $user = $this->getUser();
            $orders = $this->getShoppingService()->getUserOrders($user);

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
            $user = $this->getUser();
            $order = $this->getShoppingService()->getUserOrder($user, $orderId, true);
            
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
     * Display Shopping Cart
     * 
     * @param type $action
     * @param type $productId
     * @param type $quntity
     * @return type
     */
    public function displayShoppingCartAction()
    {
        try {
            $shoppingCartList = $this->getShoppingService()
                ->getShoppingCartList(null, true);
            
            return $this->render(
                '::web/order/shoppingCart.html.twig',
                array(
                    'shoppingCartList' => $shoppingCartList
                    )
                );            
        } catch (\Exception $ex) {
            throw $this->getException(
                'You can not view your shopping cart right now, please contact admin', 
                $ex
                );
        }
    }
    
    /**
     * Submit shopping cart, create an order request for this shopping cart.
     * clear the shopping cart session
     * 
     * @return type
     * @throws type
     */
    public function submitShoppingCartAction()
    {
        try {
            $shoppingCartList = $this->getShoppingService()
                ->getShoppingCartList();
            if (count($shoppingCartList) === 0) {
                throw new \Exception('Shopping list is empty');
            }
            
            $order = new Order();
            $order->setContent($shoppingCartList);
            $this->getShoppingService()->addEditOrder($order, Order::ORDER_TYPE_PRODUCT);
            $this->getShoppingService()->modifyShoppingCart('remove-all');
            
            return $this->redirectToRoute(
                'saman_shopping_order_set_shipping', 
                array('orderId' => $order->getId())
                );
        } catch (\Exception $ex) {
            throw $this->getException(
                'You can not submit your shopping cart right now, please contact admin', 
                $ex
                );
        }
    }
    
    /**
     * Modifying shopping cart [add, update, remove, removeAll]
     * 
     * @param Request $request
     * @param type $action
     * @param type $productId
     * @return type
     */
    public function modifyShoppingCartAction(Request $request, $action, $productId)
    {
        try {
            $quntity = intval($request->query->get('quntity', 1));
            $this->getShoppingService()
                ->modifyShoppingCart($action, $productId, $quntity);
            
            return $this->getJsonResponse(true);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'You can not add this product to your shopping cart right now, please contact admin', 
                $ex
                );
        }
    }     
    
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function addEditCustomOrderAction(Request $request, $orderId)
    {
        $user = $this->getUser();
        $order = $this->getShoppingService()->getUserOrder($user, $orderId);
        $orderConfig = $this->getParameter('saman_shopping_order');
        // Generate Product Form
        $orderForm = $this->createForm(new OrderType($orderConfig), $order);        
        $orderForm->handleRequest($request);
        // If form is submited and it is valid then add or update this $label
        if ($orderForm->isValid()) {
            
            $mediaService = $this->getService('saman_media.media');
            $this->getShoppingService()->addEditOrder(
                $order, 
                Order::ORDER_TYPE_CUSTOM,
                $mediaService
                );
            
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
     * @param Request $request
     * @return type
     */
    public function calCustomOrderPriceAction(Request $request)
    {
        try {
            $param = $request->request->get('saman_order_form');
            $data = $this->getShoppingService()->calCustomOrderPrice($param);

            $view = $this->renderView(
                '::web/order/_orderPriceInfo.html.twig', 
                array('data' => $data)
                );

            return $this->getJsonResponse(true, null, $view);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'alert.error.canNotPerformRequest', 
                $ex
                );
        }        
    }
    
    /**
     * 
     * @param type $orderId
     * @return type
     */
    public function deleteOrderAction($orderId)
    {
        try {
            $user = $this->getUser();
            $order = $this->getShoppingService()->getUserOrder($user, $orderId);
        
            if ($order->isSubmitted()) {
                // Soft-deleting an entity
                $this->getAppService()->deleteEntity($order);
            } else {
                throw new \Exception('Order is in progress');
            }
            
            return $this->getJsonResponse(true);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'You can not delete this order, please contact admin', 
                $ex
                );
        }        
    }    
    
    /**
     * 
     * @param type $orderId
     * @return type
     * @throws \Exception
     */
    public function orderConfirmationAction($orderId)
    {
        $user = $this->getUser();
        $order = $this->getShoppingService()->getUserOrder($user, $orderId, true);

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
    public function setOrderShippingAction(Request $request, $orderId)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $order = $this->getShoppingService()->getUserOrder($user, $orderId, true);
        
        $orderShippingAddress = null;
        $orderBillingAddress = null;
        $primaryShippingAddress = $user->getPrimaryShippingAddress();
        $primaryBillingAddress = $user->getPrimaryBillingAddress();
        
        // Generate Product Form
        $form = $this->createForm(new OrderShippingType($user, $order));
        $form->handleRequest($request);
        // If form is submited and it is valid then add or update this $label
        if ($form->isValid()) {
            $billingSameAsShipping = false;
            $setNewShipping = ($form->has('setNewShipping') and $form->get('setNewShipping')->getData())? true : false;
            $setNewBilling = ($form->has('setNewBilling') and $form->get('setNewBilling')->getData())? true : false;
            if (null === $primaryShippingAddress or $setNewShipping) {
                $shippingAddress = $form->get('shipping')->getData();
                $shippingAddress->setAddressType(Address::ADDRESS_TYPE_SHIPPING);
                $shippingAddress->setType(Address::TYPE_SECONDARY);
                $shippingAddress->setUser($user);
                if (null === $primaryShippingAddress) {
                    $shippingAddress->setType(Address::TYPE_PRIMARY);
                } else {
                    if ($form->has('setShippingPrimary') and $form->get('setShippingPrimary')->getData()) {
                        $shippingAddress->setType(Address::TYPE_PRIMARY);
                        if ($primaryShippingAddress instanceof Address) {
                            $primaryShippingAddress->setType(Address::TYPE_SECONDARY);
                            $em->persist($primaryShippingAddress);
                        }
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
                if (null === $primaryBillingAddress or $setNewBilling) {
                    $billingAddress = $form->get('billing')->getData();
                    $billingAddress->setAddressType(Address::ADDRESS_TYPE_BILLING);
                    $billingAddress->setType(Address::TYPE_SECONDARY);
                    $billingAddress->setUser($user);
                    if (null === $primaryBillingAddress) {
                        $billingAddress->setType(Address::TYPE_PRIMARY);
                    } else {                    
                        if ($form->has('setBillingPrimary') and $form->get('setBillingPrimary')->getData()) {
                            $billingAddress->setType(Address::TYPE_PRIMARY);
                            if ($primaryBillingAddress instanceof Address) {
                                $primaryBillingAddress->setType(Address::TYPE_SECONDARY);
                                $em->persist($primaryBillingAddress);
                            }
                        }
                    }
                    $em->persist($billingAddress);
                    $orderBillingAddress = $billingAddress;
                } else {
                    $orderBillingAddress = $primaryBillingAddress;
                }
            }
            
            if ($orderShippingAddress instanceof Address and $orderBillingAddress instanceof Address) {
                if ($order->isCustomOrder() && $form->has('payDeposit')) {
                    if ($form->get('payDeposit')->getData()) {
                        $order->setDeposit(intval($form->get('deposit')->getData()));
                    }
                }
                
                $order->setShippingAddress($orderShippingAddress);
                $order->setBillingAddress($orderBillingAddress);
                //TODO: validate deposit, $orderBillingAddress, $orderShippingAddress
                $em->flush();
                
                return $this->redirectToRoute(
                    'saman_shopping_order_payment', 
                    array('orderId' => $order->getId())
                    );
            } else {
                throw $this->createVisibleHttpException('No valid addresses are defined');
            }
        }

        return $this->render(
            '::web/order/setOrderShipping.html.twig',
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
     * @return \ShoppingBundle\Service\ShoppingService
     */
    private function getShoppingService()
    {
        return $this->getService('saman_shopping.shopping');
    }   
}