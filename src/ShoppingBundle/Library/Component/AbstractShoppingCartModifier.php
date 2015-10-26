<?php

namespace ShoppingBundle\Library\Component;

/**
 * The AbstractShoppingCartModifier class contains common methods for order serializers
 */
abstract class AbstractShoppingCartModifier
{
    /**
     * shopping cart continer
     * 
     * @var type 
     */
    private $shoppingCart = null;
    
    /**
     * 
     * @param type $shoppingCart
     */
    public function setShoppingCart($shoppingCart)
    {
        $this->shoppingCart = $shoppingCart;
    }
    
    /**
     * 
     * @return type
     */
    public function getShoppingCart()
    {
        if (null === $this->shoppingCart) {
            throw new \Exception('No shopping cart is set');
        }
        
        return $this->shoppingCart;
    }
    
    /**
     * Returns the Unix timestamp representing the date. 
     * 
     * @param \DateTime $date
     * @return type
     */
    public function getTimestamp(\DateTime $date = null)
    {
        if (null === $date) {
            $date = new \DateTime();
        }
        
        return $date->getTimestamp();
    }    

    /**
     * Add one product to shopping cart
     */
    abstract public function add($productId, $quntity = 1);
    
    /**
     * Update a product quntity in shopping cart
     */
    abstract public function update($productId, $quntity = 1);
    
    /**
     * Remove one product from shopping cart
     */
    abstract public function remove($productId);
    
    /**
     * Remove all products from shopping cart
     */
    abstract public function removeAll();
    
    /**
     * Load shopping cart content
     */
    abstract public function getContent();
    
    /**
     * Finalize shopping cart content
     */ 
    abstract public function finalize($shoppingCartList);
}