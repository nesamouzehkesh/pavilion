<?php

namespace ShoppingBundle\Library\Serializer;

use ShoppingBundle\Entity\Order;

/**
 * The AbstractOrderSerializer class contains common methods for order serializers
 */
abstract class AbstractOrderSerializer
{
    abstract public function serialize(Order $order);
}