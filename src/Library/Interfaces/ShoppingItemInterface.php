<?php

namespace Library\Interfaces;

/**
 * Description of ServiceAwareEntityInterface
 *
 * @author saman
 */
interface ShoppingItemInterface
{
    public function getTitle();
    
    public function getPrice();
    
    public function getSKU();
    
    public function getDescription($truncateLength = null);
}