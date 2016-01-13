<?php

namespace ShoppingBundle\Service;

use AppBundle\Entity\Event;
use AppBundle\Service\AppService;
use AppBundle\Service\EventHandler;
use AppBundle\Service\PaymentService;
use MediaBundle\Service\MediaService;
use UserBundle\Entity\User;
use ProductBundle\Entity\Product;
use ShoppingBundle\Entity\Order;
use ShoppingBundle\Entity\Progress;
use ShoppingBundle\Entity\OrderPayment;
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
     * @var PaymentService $paymentService
     */
    protected $paymentService;

    /**
     * 
     * @param AppService $appService
     * @param EventHandler $eventHandler
     * @param OrderProgressHandler $progressHandler
     * @param PaymentService $paymentService
     * @param type $parameters
     */
    public function __construct(
        AppService $appService, 
        EventHandler $eventHandler,
        OrderProgressHandler $progressHandler,
        PaymentService $paymentService,
        $parameters
        ) 
    {
        $this->appService = $appService;
        $this->eventHandler = $eventHandler;
        $this->progressHandler = $progressHandler;
        $this->paymentService = $paymentService;
        $this->appService->setParametrs($parameters);
        $this->em = $appService->getEntityManager();
    }
    
    /**
     * Create order payment entity with status of STATUS_CREATED. It returns a 
     * persisted payment entity. Then we use this payment entity in payment service
     * to create and send a payment request.
     * 
     * @param Order $order
     * @return type
     */
    public function createOrderPayment(Order $order, $paymentType = OrderPayment::TYPE_PAY_PAL)
    {
        // Call total price
        $totalPrice = $toBePay = $order->callTotalPrice();
        if (null !== $order->getDeposit() && $order->isCustomOrder()) {
            $toBePay = $order->getDeposit();
        }
        
        // Make a payment object
        $payment = $this->getOrderPayment();
        $payment->setStatus(OrderPayment::STATUS_CREATED);
        $payment->setOrder($order);
        $payment->setCurrency($order->getCurrency());
        $payment->setUser($order->getUser());
        $payment->setType($paymentType);
        $payment->setContent(array());
        $payment->setValue($toBePay);
        $itemList = $payment->getPaymentSerializer()->serialize($order);
        $payment->setItemList($itemList);
        
        // Update order total price
        $order->setTotalPrice($totalPrice);
        $order->addPayment($payment);
        
        $this->appService->persistEntity($payment);
        $this->appService->persistEntity($order);
        $this->appService->flushEntityManager();

        return $payment;
    }
    
    /**
     * After payment request successfully is sent we finalize user order and its 
     * payment
     *
     * @param OrderPayment $payment
     * @param type $param
     * @return boolean
     */
    public function finalizeOrderPayment(OrderPayment $payment, $param = array())
    {
        $payment->setContent($param);
        $order = $payment->getOrder();
        $order->setIsPaid(true);
        
        // Finalize order content
        $this->finalizeOrderContent($order);
        $this->appService->persistEntity($order);
        
        // Handle the order progress
        $this->progressHandler->handleProgress($order, Progress::PROGRESS_PAID);
        
        // Handle the event related to this action
        $this->eventHandler->handleEvent($order, Event::TR_PAYMENT_ORDER);     
        $this->appService->flushEntityManager();
            
        return true;
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
            $this->loadOrder($order);
        }
        
        return $order;
    }
    
    /**
     * Load order content
     * 
     * @param Order $order
     */
    public function loadOrder(Order $order)
    {
        if ($order->isProductOrder()) {
            $shoppingList = $this->getShoppingCartList($order, true);
            $order->setLoadedContent($shoppingList);
        } else {
            $order->setLoadedContent($order->getContent());
        }
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
        // Get the content of shopping list
        if ($order instanceof Order) {
            $shoppingList = $order->getContent();
        } else {
            $shoppingCart = new ShoppingCart(
                new ShoppingSessionCartModifier(),
                $this->appService->getSession()
                );

            $shoppingList = $shoppingCart->getContent();            
        }            
        
        // If $validate is set to true then validate the content of shopping list 
        // with current products in DB
        if (!$validate) {
            return $shoppingList;
        }
        
        // Validate the content of shopping list
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
    
    /**
     * Get a user payment
     * @scope user
     * 
     * @param type $paymentId
     * @return OrderPayment
     * @throws \Exception
     */
    public function getUserOrderPayment(User $user, $paymentId = null)
    {
        return $this->getOrderPayment($paymentId, $user);
    }
    
    /**
     * Get a payment
     * @scope admin
     * 
     * @param type $paymentId
     * @return OrderPayment
     * @throws \Exception
     */
    public function getOrderPayment($paymentId = null, User $user = null)
    {
        if (null === $paymentId) {
            return new OrderPayment;
        }
        
        $em = $this->appService->getEntityManager();
        $payment = OrderPayment::getRepository($em)->getPayment($paymentId, $user);
        if (!$payment instanceof OrderPayment) {
            throw $this->appService->createVisibleHttpException('No payment has been found');
        }
        
        return $payment;
    }
    
    /**
     * Calculate the custom order price based on user form parameters
     * 
     * @param type $param
     * @return type
     */
    public function calCustomOrderPrice($param)
    {
        if (!isset($param['content'])) {
            return null;
        }
        $content = $param['content'];
        if (!isset($content['width']) || !isset($content['height'])) {
            return null;
        }
        if (0 === intval($content['width']) || 0 === intval($content['height'])) {
            return null;
        }        
        
        $orderStructure = $this->appService->getParameter('orderStructure');
        $height = intval($content['height']);
        $width = intval($content['width']);
        
        if (isset($orderStructure['dpi']['choices'][$content['dpi']])) {
            $dpi = $orderStructure['dpi']['choices'][$content['dpi']];
        } else {
            throw $this->appService->createVisibleHttpException('Wrong Dpi');
        }
        if (isset($orderStructure['colors']['choices'][$content['colors']])) {
            $colors = $orderStructure['colors']['choices'][$content['colors']];
        } else {
            throw $this->appService->createVisibleHttpException('Wrong color');
        }
        if (isset($orderStructure['numberOfColors']['choices'][$content['numberOfColors']])) {
            $numberOfColors = $orderStructure['numberOfColors']['choices'][$content['numberOfColors']];
        } else {
            throw $this->appService->createVisibleHttpException('Wrong number of colors');
        }

        $knots =  $height *  $width * $dpi;
        $price = $knots * 0.014;
        $duration = 12;

        return array(
            'price' => $price,
            'duration' => $duration,
            'knots' => $knots,
            'numberOfColors' => $numberOfColors,
        );
    }
    
    /**
     * Finalize order content
     * 
     * @param Order $order
     */
    private function finalizeOrderContent(Order $order)
    {
        $this->loadOrder($order);
        
        if ($order->isProductOrder()) {
            $shoppingCart = new ShoppingCart(
                new ShoppingSessionCartModifier(),
                $this->appService->getSession()
                );
            $orderItems = $order->getLoadedContent();
            $finalizedOrderItems = $shoppingCart->finalize($orderItems);
            $order->setContent($finalizedOrderItems);
            foreach ($orderItems as $orderItem) {
                $product = $orderItem['product'];
                $product->addOrder($order);
                $order->addProduct($product);
            }
        }        
    }    
}