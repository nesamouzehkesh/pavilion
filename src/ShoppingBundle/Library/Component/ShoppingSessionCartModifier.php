<?php

namespace ShoppingBundle\Library\Component;

use ShoppingBundle\Entity\Order;

/**
 * The ShoppingSessionCartModifier
 */
class ShoppingSessionCartModifier extends AbstractShoppingCartModifier
{
    public function getContent()
    {
        $shoppingCartSession = $this->getShoppingCart();
        if (!$shoppingCartSession->has(self::SHOPPING_CART_NAME)) {
            return array();
        }
        
        return $shoppingCartSession->get(self::SHOPPING_CART_NAME);
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