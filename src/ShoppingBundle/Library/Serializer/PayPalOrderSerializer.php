<?php

namespace ShoppingBundle\Library\Serializer;

use ShoppingBundle\Entity\Order;

/**
 * A very Simple FormSerializer
 */
class PayPalOrderSerializer extends AbstractOrderSerializer
{
    public function serialize(Order $order)
    {
        return array(
            'items' => $this->getOrderItems($order),
            'shipping_address' => $this->getOrderShippingAddress($order)
        );
    }
    
    /**
     * Get serialized order items
     * 
     * @param Order $order
     */
    private function getOrderItems(Order $order)
    {
        $orderContent = $order->getContent();        
    }
    
    /**
     * Get serialized shipping address
     * 
     * @param Order $order
     */
    private function getOrderShippingAddress(Order $order)
    {
        $address = $order->getShippingAddress();
        
        return array(
            'recipient_name' => $address->getFullName(),
            'type' => $address->getLocationTypeLabel(array('residential', 'business', 'mailbox')),
            'line1' => $address->getFirstAddressLine(),
            'line2' => $address->getSecondAddressLine(),
            'city' => $address->getCity(),
            'country_code' => $address->getCountry(),
            'postal_code' => $address->getPostCode(),
            'state' => $address->getState(),
            'phone' => $address->getPhoneNumber()
        );
    }
}