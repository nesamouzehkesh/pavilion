<?php

namespace ShoppingBundle\Library\Serializer;

use Library\Interfaces\ShoppingItemInterface;
use ShoppingBundle\Entity\Order;
use UserBundle\Entity\Address;

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
        if ($order->isCustomOrder()) {
            $orderItems = array(
                array(
                    'quantity' => (string) $order->getQuantity(),
                    'name' => $order->getTitle(),
                    'price' => (string) $order->getTotalPrice(),
                    'currency' => $order->getCurrency(),
                    'sku' => $order->getSKU(),
                    'description' => $order->getDescription(127),
                )
            );
        } else {
            $orderItems = array();
            foreach ($order->getLoadedContent() as $item) {
                $product = $item['product'];
                if (!$product instanceof ShoppingItemInterface) {
                    continue;
                }            

                $orderItems[] = array(
                    'quantity' => (string) $item['qty'],
                    'name' => $product->getTitle(),
                    'price' => (string) $product->getPrice(),
                    'currency' => $order->getCurrency(),
                    'sku' => $product->getSKU(),
                    'description' => $product->getDescription(127),
                );
            }
        }
        
        return $orderItems;
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
            'type' => $address->getLocationTypeLabel(array(
                Address::LOCATION_TYPE_RESIDENTIAL => 'residential', 
                Address::LOCATION_TYPE_BUSINESS => 'business', 
                Address::LOCATION_TYPE_MAILBOX => 'mailbox'
                )),
            'line1' => $address->getFirstAddressLine(true),
            'line2' => $address->getSecondAddressLine(true),
            'city' => $address->getCity(),
            'country_code' => $address->getCountry(),
            'postal_code' => $address->getPostCode(),
            'state' => $address->getState(),
            'phone' => $address->getPhoneNumber()
        );
    }
}