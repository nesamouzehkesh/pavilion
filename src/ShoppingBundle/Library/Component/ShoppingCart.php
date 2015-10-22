<?php

namespace ShoppingBundle\Library\Component;

/**
 * The SessionShoppingCartModifier
 */
class ShoppingCart
{
    /**
     *
     * @var AbstractShoppingCartModifier Shopping Cart Modifier 
     */
    private $modifier;
    
    /**
     * 
     * @param \ShoppingBundle\Library\Component\AbstractShoppingCartModifier $modifier
     * @param type $shoppingCart
     */
    public function __construct(AbstractShoppingCartModifier $modifier, $shoppingCart)
    {
        $modifier->setShoppingCart($shoppingCart);
        $this->modifier = $modifier;
    }
    
    /**
     * 
     * @return type
     */
    public function getContent()
    {
        return $this->modifier->getContent();
    }

    /**
     * 
     * @param type $action
     * @param type $productId
     * @param type $quntity
     * @throws \Exception
     */
    public function modify($action, $productId = null, $quntity = 1)
    {
        if ($quntity === 0) {
            throw new \Exception('Your order quantity can not be zero');
        }
        
        if (in_array($action, array('add', 'update', 'remove')) and 0 === intval($productId)) {
            throw new \Exception('No product ID is provide for this shopping cart action');
        }
        
        switch ($action) {
            case 'add':
                    $this->modifier->add($productId, $quntity);
                break;
            case 'update':
                    $this->modifier->update($productId, $quntity);
                break;
            case 'remove':
                    $this->modifier->remove($productId);
                break;
            case 'remove-all':
                    $this->modifier->removeAll();
                break;
            default :
                throw new \Exception('Invalid action is defined for shopping cart modifier');
        }
    }
}