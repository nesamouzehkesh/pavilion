<?php

namespace Library\Api\Payment;

/**
 * Description of ServiceAwareEntityInterface
 *
 * @author saman
 */
interface PaymentEntityInterface
{
    // Payment status
    const STATUS_CREATED = 1;
    const STATUS_REJECT = 2;
    const STATUS_FINALIZED = 3;
    
    // Payment types
    const TYPE_PAY_PAL = 1;
    const TYPE_CREDIT_CARD = 2;
    
    /**
     * Get payment total value
     */
    public function getValue();
    
    /**
     * Get date
     *
     * @return integer 
     */
    public function getDate();

    /**
     * Get payment type
     */
    public function getType();
    
    /**
     * Get payment status
     */
    public function getStatus();
    
    /**
     * Get payment user
     */
    public function getUser();
    
    /**
     * Get payment currency
     */
    public function getCurrency();
    
    /**
     * Get payment items list
     */
    public function getItemList();
    
    /**
     * Get payment description
     * 
     * @param type $truncateLength
     */
    public function getDescription($truncateLength = null);
    
    /**
     * Get the serializer of payment linked entity 
     */
    public function getPaymentSerializer();
}
