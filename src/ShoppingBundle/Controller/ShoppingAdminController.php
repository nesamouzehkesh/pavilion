<?php

namespace ShoppingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Library\Base\BaseController;
use ShoppingBundle\Entity\Progress;
use ShoppingBundle\Entity\Order;
use ShoppingBundle\Entity\OrderProgress;
use ShoppingBundle\Form\OrderProgressType;

class ShoppingAdminController extends BaseController
{
    /**
     * 
     * @return type
     */
    public function displayOrdersAction(Request $request)
    {
        try {
            // Get search parameters from HTTP request
            $searchParam = $this->getAppService()
                ->getSearchParam($request, array('progressFilter', 'typeFilter'));            
            
            // Get all Orders
            $orders = $this->getShoppingService()->getOrders($searchParam);

            // Get pagination
            $ordersPagination = $this->getAppService()
                ->paginate($request, $orders);

            // Get the view of pages list
            $ordersView = $this->renderView(
                'ShoppingBundle:Shopping:element/orders.html.twig',
                array('ordersPagination' => $ordersPagination)
                );

            // If user use the pagination to view other pages then we just return the 
            // $pagesView as a jason response array
            if ($request->get('headless')) {
                return $this->getAppService()->getJsonResponse(true, null, $ordersView);
            } 
            
            return $this->render(
                'ShoppingBundle:Shopping:index.html.twig',
                array(
                    'ordersView' => $ordersView,
                    'staticProgresses' => Progress::$staticProgresses
                    )
                );
        } catch (Exception $ex) {
            return $this->getExceptionResponse('There is a problem in displaying your orders', $ex);
        }
    }
    
    /**
     * 
     * @param Request $request
     * @param type $orderId
     * @return type
     */
    public function displayOrderAction($orderId)
    {
        try {
            // Get all Products
            $order = $this->getShoppingService()->getOrder($orderId);
            
            // Render and return the view
            return $this->render(
                'ShoppingBundle:Shopping:order.html.twig',
                array(
                    'order' => $order,
                    'orderEvents' => $this->getEventHandler()->getEvents($order),
                    'orderConfig' => $this->getParameter('saman_shopping_order')
                    )
                );
        } catch (\Exception $ex) {
            throw $this->getException(
                'alert.error.canNotDisplayItem', 
                $ex
                );
        }
    }  
    
    /**
     * Delete a Order
     * 
     * @param type $orderId
     * @return type
     */
    public function deleteOrderAction($orderId)
    {
        try {
            // Get Product
            $order = $this->getShoppingService()->getOrder($orderId);
            
            // Soft-deleting an entity
            $this->getAppService()->deleteEntity($order);

            return $this->getJsonResponse(true);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'alert.error.canNotDeleteItem', 
                $ex
                );
        }        
    }
    
    /**
     * Delete a OrderProgress
     * 
     * @param type $progressId
     * @return type
     */
    public function deleteOrderProgressAction($progressId)
    {
        try {
            // Get Product
            $orderProgress = $this->getOrderProgressHandler()
                ->getOrderProgress($progressId);            
            // Soft-deleting an entity
            $this->getAppService()->deleteEntity($orderProgress);

            return $this->getJsonResponse(true);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
                'alert.error.canNotDeleteItem', 
                $ex
                );
        }        
    }    
    /**
     * Display and handel add edit product action
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function addEditOrderProgressAction(Request $request, $orderId, $progressId = null)
    {
        try {
            // Get product object
            $order = $this->getShoppingService()->getOrder($orderId);
            $orderProgress = $this->getOrderProgressHandler()
                ->getOrderProgress($progressId, $order);

            // Generate Product Form
            $orderProgressForm = $this->createForm(
                new OrderProgressType(), 
                $orderProgress,
                array(
                    'action' => $request->getUri(),
                    'method' => 'post'
                    )
                );

            $orderProgressForm->handleRequest($request);
            // If form is submited and it is valid then add or update this $product
            if ($orderProgressForm->isValid()) {
                if ($this->isOrderProgressFormValid($orderProgressForm, $order)) {
                    $mediaService = $this->getService('saman_media.media');
                    $this->getAppService()
                        ->setMediaService($mediaService)
                        ->saveMedia($orderProgress);

                    return $this->getJsonResponse(true);
                }
            }

            $view = $this->renderView(
                'ShoppingBundle:Shopping:/form/orderProgress.html.twig', 
                array('form' => $orderProgressForm->createView())
                );
            
            return $this->getJsonResponse(true, null, $view);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse('Can not add or edit order progress', $ex);
        }         
    }    
    
    /**
     * Check if customer form data is valid
     * 
     * @param type $customerForm
     * @return boolean
     */
    private function isOrderProgressFormValid($orderProgressForm, Order $order)
    {
        // Get some extra form field that are not mapped to user object. 
        $status = $orderProgressForm->get('status')->getData();
        if (OrderProgress::STATUS_INPROGRESS === $status and null !== $order->getActiveProgress()) {
            $this->addFormError(
                $orderProgressForm->get('status'), 
                'You cannot have multiple in progress process'
                );
            
            return false;
        }
        
        return true;
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
    
    /**
     * Get OrderProgressHandler service
     * 
     * @return \ShoppingBundle\Service\OrderProgressHandler
     */
    private function getOrderProgressHandler()
    {
        return $this->getService('saman_shopping.orderProgressHandler');
    }
}