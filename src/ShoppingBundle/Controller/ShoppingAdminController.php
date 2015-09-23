<?php

namespace ShoppingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Library\Base\BaseController;

class ShoppingAdminController extends BaseController
{
    /**
     * 
     * @return type
     */
    public function displayOrdersAction(Request $request)
    {
        try {
            
            // Get all Orders
            $orders = $this->getOrderService()->getOrders();

            // Get pagination
            $ordersPagination = $this->getAppService()
                ->paginate($request, $orders);

            // Render and return the view
            return $this->render(
                'ShoppingBundle:Shopping:orders.html.twig',
                array('ordersPagination' => $ordersPagination)
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
            $order = $this->getOrderService()->getOrder($orderId);
            
            // Render and return the view
            return $this->render(
                'ShoppingBundle:Shopping:order.html.twig',
                array('order' => $order)
                );
        } catch (\Exception $ex) {
            return $this->getExceptionResponse(
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
            $order = $this->getOrderService()->getOrder($orderId);
            
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
     * Display and handel add edit product action
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return type
     */
    public function addEditOrderProgressAction(Request $request, $orderId, $progressId = null)
    {
        try {
            // Get product object
            $order = $this->getOrderService()->getOrder($orderId);
            $progress = $this->getProductEntity($progressId);

            // Generate Product Form
            $productForm = $this->createForm(
                new ProductType(), 
                $product,
                array(
                    'action' => $request->getUri(),
                    'method' => 'post'
                    )
                );

            $productForm->handleRequest($request);
            // If form is submited and it is valid then add or update this $product
            if ($productForm->isValid()) {
                
                $mediaService = $this->getService('saman_media.media');
                $this->getAppService()
                    ->setMediaService($mediaService)
                    ->saveMedia($product);
                
                return $this->getJsonResponse(true);
            }

            $view = $this->renderView(
                'ProductBundle:Product:/form/product.html.twig', 
                array('form' => $productForm->createView())
                );
            
            return $this->getJsonResponse(true, null, $view);
        } catch (\Exception $ex) {
            return $this->getExceptionResponse('Can not add or edit product', $ex);
        }         
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