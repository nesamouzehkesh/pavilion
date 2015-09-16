<?php

namespace ShoppingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Library\Base\BaseController;

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
            return $this->getOrderService()->displayOrders();
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
            return $this->getOrderService()->displayOrder($orderId);
        } catch (Exception $ex) {
            return $this->getExceptionResponse('There is a problem in displaying your order', $ex);
        }    
    }
    
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function addOrderAction(Request $request)
    {
        try {
            return $this->getOrderService()->addOrder($request);
        } catch (Exception $ex) {
            return $this->getExceptionResponse('There is a problem in making your order', $ex);
        }        
    }
    
    /**
     * 
     * @param type $pageUrl
     * @return type
     */
    public function editOrderAction(Request $request, $orderId)
    {
        try {
            return $this->getOrderService()->editOrder($request, $orderId);
        } catch (Exception $ex) {
            return $this->getExceptionResponse('There is a problem in updating your order', $ex);
        }        
    }
    
    /**
     * 
     * @return \ShoppingBundle\Service\OrderService
     */
    private function getOrderService()
    {
        return $this->getService('saman_shopping.order');
    }    
}