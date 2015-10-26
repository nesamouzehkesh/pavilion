<?php

namespace ShoppingBundle\Library\Component;

use Library\Interfaces\ShoppingItemInterface;
use ShoppingBundle\Entity\Order;

/**
 * The ShoppingSessionCartModifier
 */
class ShoppingSessionCartModifier extends AbstractShoppingCartModifier
{
    const SHOPPING_CART_NAME = "shopping-cart";
    
    /**
     * Get shopping cart content
     * 
     * @return type
     */
    public function getContent()
    {
        $shoppingCartSession = $this->getShoppingCart();
        if (!$shoppingCartSession->has(self::SHOPPING_CART_NAME)) {
            return array();
        }
        
        return $shoppingCartSession->get(self::SHOPPING_CART_NAME);
    }
    
    /**
     * Finalize shopping cart content
     * 
     * @param type $orderList
     */
    public function finalize($orderList)
    {
        $orderItems = array();
        foreach ($orderList as $orderItem) {
            $product = $orderItem['product'];
            if (!$product instanceof ShoppingItemInterface) {
                throw new \Exception('Shopping cart is not loaded properly with products');
            }
            
            $orderItems[$product->getId()] = array(
                'title' => $product->getTitle(),
                'price' => $product->getPrice(),
                'originalPrice' => $product->getOriginalPrice(),
                'type' => Order::ORDER_TYPE_PRODUCT,
                'date' => $orderItem['date'],
                'qty' => intval($orderItem['qty'])
            );
        }
        
        return $orderItems;
    }
    
    /**
     * Add one product to shopping cart
     * 
     * @param type $productId
     * @param type $quntity
     * @return \ShoppingBundle\Library\Component\SessionShoppingCartModifier
     * @throws \Exception
     */
    public function add($productId, $quntity = 1)
    {
        $orderList = $this->getContent();
        $orderList[$productId] = array(
            'type' => Order::ORDER_TYPE_PRODUCT,
            'date' => $this->getTimestamp(),
            'qty' => $quntity
        );
        
        $shoppingCart = $this->getShoppingCart();
        $shoppingCart->set(self::SHOPPING_CART_NAME, $orderList);
        
        return $this;
    }
    
    /**
     * Update a product quntity in shopping cart
     * 
     * @param type $productId
     * @param type $quntity
     * @return \ShoppingBundle\Library\Component\SessionShoppingCartModifier
     * @throws \Exception
     */
    public function update($productId, $quntity = 1)
    {
        $shoppingCart = $this->getShoppingCart();
        $orderList = $this->getContent();
        
        if (isset($orderList[$productId])) {
            $orderList[$productId] = array(
                'type' => Order::ORDER_TYPE_PRODUCT,
                'date' => $this->getTimestamp(),
                'qty' => $quntity
            );

            $shoppingCart->set(self::SHOPPING_CART_NAME, $orderList);
        }
        
        return $this;
    }
    
    /**
     * Remove one product from shopping cart
     * 
     * @param type $productId
     * @return \ShoppingBundle\Library\Component\SessionShoppingCartModifier
     * @throws \Exception
     */
    public function remove($productId)
    {
        $shoppingCart = $this->getShoppingCart();
        $orderList = $this->getContent();
        
        if (isset($orderList[$productId])) {
            unset($orderList[$productId]);
            $shoppingCart->set(self::SHOPPING_CART_NAME, $orderList);
        }
        
        return $this;
    }
    
    /**
     * Remove all products from shopping cart
     * 
     * @return \ShoppingBundle\Library\Component\SessionShoppingCartModifier
     */
    public function removeAll()
    {
        $shoppingCart = $this->getShoppingCart();
        if ($shoppingCart->has(self::SHOPPING_CART_NAME)) {
            $shoppingCart->remove(self::SHOPPING_CART_NAME);
        }
        
        return $this;
    }
}