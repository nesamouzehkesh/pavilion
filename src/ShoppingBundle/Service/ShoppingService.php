<?php

namespace ShoppingBundle\Service;

use AppBundle\Entity\Event;
use AppBundle\Service\AppService;
use AppBundle\Service\EventHandler;
use MediaBundle\Service\MediaService;
use UserBundle\Entity\User;
use ProductBundle\Entity\Product;
use ShoppingBundle\Entity\Order;
use ShoppingBundle\Entity\Progress;
use ShoppingBundle\Service\OrderProgressHandler;

class ShoppingService
{
    const SHOPPING_CART_NAME = "shopping-cart";
    
    /**
     * @var AppService $appService
     */
    protected $appService;
    
    /**
     * @var EventHandler $eventHandler
     */
    protected $eventHandler;
    
    /**
     * @var OrderProgressHandler $progressHandler
     */
    protected $progressHandler;

    /**
     * 
     * @param AppService $appService
     * @param EventHandler $eventHandler
     * @param OrderProgressHandler $progressHandler
     * @param type $parameters
     */
    public function __construct(
        AppService $appService, 
        EventHandler $eventHandler,
        OrderProgressHandler $progressHandler,
        $parameters
        ) 
    {
        $this->appService = $appService;
        $this->eventHandler = $eventHandler;
        $this->progressHandler = $progressHandler;
        $this->appService->setParametrs($parameters);
    }
    
    /**
     * 
     * @param Order $order
     * @param type $mediaService
     */
    public function addEditOrder(Order $order, $type, MediaService $mediaService = null)
    {
        $this->appService->transactionBegin();
        
        try {
            $isNew = (null === $order->getId());
            $order->setType($type);
            $order->setUser($this->appService->getUser());
            
            if ($type === Order::ORDER_TYPE_CUSTOM) {
                $this->appService
                    ->setMediaService($mediaService)
                    ->saveMedia($order);
            } else {
                $this->appService->saveEntity($order);
            }

            // Handle the SUBMITTED progress order
            if ($isNew) {
                $this->progressHandler
                    ->handleProgress($order, Progress::PROGRESS_SUBMITTED);
            }
            
            // Handle the event related to this action
            $this->eventHandler->handleEvent(
                $order, 
                $isNew? Event::TR_ADD_ORDER : Event::TR_EDIT_ORDER
                );
            $this->appService->transactionCommit();
        } catch (\Exception $ex) {
            $this->appService->transactionRollback();
            
            throw $ex;
        }            
    }
    
    /**
     * 
     * @param User $user
     * @param type $params
     * @return type
     */
    public function getUserOrders(User $user, $params = array())
    {
        return Order::getRepository($this->appService->getEntityManager())
            ->getUserOrders($user, $params);        
    }
    
    /**
     * 
     * @param type $params
     * @param type $justQuery
     * @return type
     */
    public function getOrders($params = array(), $justQuery = true)
    {
        return Order::getRepository($this->appService->getEntityManager())
            ->getOrders($params, $justQuery);
    }
    
    /**
     * 
     * @param User $user
     * @param type $orderId
     * @return type
     */
    public function getUserOrder(User $user, $orderId = null, $loadContentInfo = true)
    {
        $order = $this->getOrder($orderId, $user);
        if ($loadContentInfo and $order->isProductOrder()) {
            $shoppingCartList = $this->getShoppingCartList(true, $order);
            $order->setContent($shoppingCartList);
        }
        
        return $order;
    }
    
    /**
     * Get an order
     * 
     * @param type $orderId
     * @return Order
     * @throws \Exception
     */
    public function getOrder($orderId = null, User $user = null)
    {
        if (null === $orderId) {
            return new Order();
        }
        
        $order = Order::getRepository($this->appService->getEntityManager())
            ->getOrder($orderId, $user);
        if (!$order instanceof Order) {
            throw $this->appService->createVisibleHttpException('No order has been found');
        }            
        
        return $order;
    }
    
    /**
     * 
     * @param Order $order
     * @return type
     */
    public function getShoppingCartListIds(Order $order = null)
    {
        if (null !== $order) {
            $shoppingCartList = $order->getContent();
        } else {
            $session = $this->appService->getSession();
            if (!$session->has(self::SHOPPING_CART_NAME)) {
                return array();
            }
            $shoppingCartList = $session->get(self::SHOPPING_CART_NAME);
        }
        if (!is_array($shoppingCartList)) {
            return array();
        }
        
        return array_map(function($item) {
            if (isset($item['id'])) {
                return $item['id'];
            }
        }, $shoppingCartList);
    }
    
    /**
     * 
     * @param type $setProduct
     * @return type
     */
    public function getShoppingCartList($setProduct = true, Order $order = null)
    {
        // Get shopping list from order object if it is provided. Otherwise try 
        // to get it from session
        if (null !== $order) {
            $shoppingCartList = $order->getContent();
        } else {
            $session = $this->appService->getSession();
            if (!$session->has(self::SHOPPING_CART_NAME)) {
                return array();
            }
            $shoppingCartList = $session->get(self::SHOPPING_CART_NAME);
        }
        
        if (!is_array($shoppingCartList)) {
            return array();
        }
        $productIds = array();
        foreach ($shoppingCartList as $productOrder) {
            if (isset($productOrder['id'])) {
                $productIds[] = $productOrder['id'];
            }
        }
        if (count($productIds) === 0) {
            return array();
        }
        
        $validShoppingCartList = array();
        // Get products based on $productIds
        $products = Product::getRepository($this->appService->getEntityManager())
            ->getProductsById($productIds);
        foreach ($products as $product) {
            $productId = $product->getId();
            if (isset($shoppingCartList[$productId])) {
                $validShoppingCartList[$productId] = $shoppingCartList[$productId];
                if ($setProduct) {
                    $validShoppingCartList[$productId]['product'] = $product;
                }
            }
        }
        
        return $validShoppingCartList;
    }
    
    /**
     * 
     * @param type $order
     */
    public function finalizeOrderShoppingCartList($order)
    {
        $totalPrice = 0;
        $orderItems = array();
        foreach ($this->getShoppingCartList(true, $order) as $orderItem) {
            $product = $orderItem['product'];
            $productId = $product->getId();
            $quntity = intval($orderItem['qty']);
            if ($quntity === 0) {
                throw new \Exception('Your order quantity can not be zero');
            }
            
            $itemPrice = $product->getPrice() * $quntity;
            $totalPrice = $totalPrice + $itemPrice;
            
            $orderItems[$productId] = array(
                'id' => $productId,
                'title' => $product->getTitle(),
                'price' => $product->getPrice(),
                'originalPrice' => $product->getOriginalPrice(),
                'totalPrice' => $itemPrice,
                'type' => Order::ORDER_TYPE_PRODUCT,
                'date' => $orderItem['date'],
                'qty' => $quntity
            );
        }

        $order->setContent($orderItems);
        
        return $totalPrice;
    }

    /**
     * Modifying shopping cart [add, update, remove, removeAll]
     * 
     * @param type $action
     * @param type $productId
     * @param type $quntity
     * @return boolean
     * @throws \Exception
     */
    public function modifyShoppingCart($action, $productId = null, $quntity = 1)
    {
        if ($quntity === 0) {
            throw new \Exception('Your order quantity can not be zero');
        }
            
        $session = $this->appService->getSession();
        switch ($action) {
            case 'add':
                if (null === $productId) {
                    throw new \Exception('No product ID is provide for set shopping cart action');
                }
                
                $productOrderList = array();
                if ($session->has(self::SHOPPING_CART_NAME)) {
                    $productOrderList = $session->get(self::SHOPPING_CART_NAME);
                    if (!is_array($productOrderList)) {
                        $productOrderList = array();
                    }
                }
                
                $productOrderList[$productId] = array(
                    'id' => $productId,
                    'type' => Order::ORDER_TYPE_PRODUCT,
                    'date' => $this->appService->getTimestamp(),
                    'qty' => $quntity
                );
                $session->set(self::SHOPPING_CART_NAME, $productOrderList);
                break;
            case 'update':
                    if (null === $productId) {
                        throw new \Exception('No product ID is provide for update shopping cart action');
                    }
                    
                    if ($session->has(self::SHOPPING_CART_NAME)) {
                        $productOrderList = $session->get(self::SHOPPING_CART_NAME);
                        if (isset($productOrderList[$productId])) {
                            $productOrderList[$productId] = array(
                                'id' => $productId,
                                'type' => Order::ORDER_TYPE_PRODUCT,
                                'date' => $this->appService->getTimestamp(),
                                'qty' => $quntity
                            );
                            $session->set(self::SHOPPING_CART_NAME, $productOrderList);
                        }
                    }                
                break;
            case 'remove':
                    if (null === $productId) {
                        throw new \Exception('No product ID is provide for delete shopping cart action');
                    }
                    
                    if ($session->has(self::SHOPPING_CART_NAME)) {
                        $productOrderList = $session->get(self::SHOPPING_CART_NAME);
                        if (isset($productOrderList[$productId])) {
                            unset($productOrderList[$productId]);
                            $session->set(self::SHOPPING_CART_NAME, $productOrderList);
                        }
                    }                
                break;
            case 'remove-all':
                    if ($session->has(self::SHOPPING_CART_NAME)) {
                        $session->remove(self::SHOPPING_CART_NAME);
                    }
                break;
            default :
                throw new \Exception('Invalid action is defined for modifying shopping cart');
        }
        
        return true;
    }    
}