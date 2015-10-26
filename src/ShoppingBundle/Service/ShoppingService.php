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
use ShoppingBundle\Library\Component\ShoppingCart;
use ShoppingBundle\Library\Component\ShoppingSessionCartModifier;

class ShoppingService
{
    /**
     *
     * @var type
     */
    protected $em;

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
        $this->em = $appService->getEntityManager();
    }
    
    /**
     * Finalize shopping cart
     * 
     * @param Order $order
     * @return type
     */
    public function finalizeShoppingCart(Order $order)
    {
        $shoppingCartList = $this->getShoppingCartList($order, true);
        $shoppingCart = new ShoppingCart(
            new ShoppingSessionCartModifier(),
            $this->appService->getSession()
            );
        
        return $shoppingCart->finalize($shoppingCartList);
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
        $shoppingCart = new ShoppingCart(
            new ShoppingSessionCartModifier(),
            $this->appService->getSession()
            );
        
        $shoppingCart->modify($action, $productId, $quntity);
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
        return Order::getRepository($this->em)->getUserOrders($user, $params);        
    }
    
    /**
     * 
     * @param type $params
     * @param type $justQuery
     * @return type
     */
    public function getOrders($params = array(), $justQuery = true)
    {
        return Order::getRepository($this->em)->getOrders($params, $justQuery);
    }
    
    /**
     * 
     * @param User $user
     * @param type $orderId
     * @return type
     */
    public function getUserOrder(User $user, $orderId = null, $loadOrderContent = false)
    {
        return $this->getOrder($orderId, $user, $loadOrderContent);
    }
    
    /**
     * Get an order or create a new one
     * 
     * @param type $orderId
     * @param User $user
     * @param type $loadOrderContent
     * @return Order
     * @throws type
     */
    public function getOrder($orderId = null, User $user = null, $loadOrderContent = false)
    {
        if (null === $orderId) {
            return new Order();
        }
        
        $order = Order::getRepository($this->em)->getOrder($orderId, $user);
        if (!$order instanceof Order) {
            throw $this->appService->createVisibleHttpException('No order has been found');
        }
        
        if ($loadOrderContent) {
            if ($order->isProductOrder()) {
                $order->setLoadedContent($this->getShoppingCartList($order, true));
            } else {
                $order->setLoadedContent($order->getContent());
            }
        }
        
        return $order;
    }
    
    /**
     * 
     * @param Order $order
     * @return type
     */
    public function getShoppingCartListIds(Order $order = null, $validate = false)
    {
        return array_keys($this->getShoppingCartList($order, false, $validate));
    }
    
    /**
     * Get a valid shopping cart list and load it with product objects
     * 
     * @param Order $order
     * @param type $setProduct
     * @return type
     */
    public function getShoppingCartList(Order $order = null, $setProduct = false, $validate = true)
    {
        if ($order instanceof Order) {
            $shoppingList = $order->getContent();
        } else {
            $shoppingCart = new ShoppingCart(
                new ShoppingSessionCartModifier(),
                $this->appService->getSession()
                );

            $shoppingList = $shoppingCart->getContent();            
        }            
        
        if (!$validate) {
            return $shoppingList;
        }
            
        $validShoppingList = array();
        // Get products based on $productIds
        $products = Product::getRepository($this->em)->getProductsById(
            array_keys($shoppingList));
        foreach ($products as $product) {
            $productId = $product->getId();
            $validShoppingList[$productId] = $shoppingList[$productId];
            if ($setProduct) {
                $validShoppingList[$productId]['product'] = $product;
            }
        }
        
        return $validShoppingList;
    }
}