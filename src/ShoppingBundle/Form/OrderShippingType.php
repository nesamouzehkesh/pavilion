<?php

namespace ShoppingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use UserBundle\Form\AddressType;
use UserBundle\Entity\User;
use ShoppingBundle\Entity\Order;

/**
 * 
 */
class OrderShippingType extends AbstractType
{
    /**
     * @var User $user
     */
    private $user;
    
    /**
     * @var Order $order
     */
    private $order;
        
    /**
     * @param User $user
     */
    public function __construct(User $user, Order $order)
    {
        $this->user = $user;
        $this->order = $order;
    }
    
    /**
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('shipping', new AddressType($this->user))
            ->add('billing', new AddressType($this->user))
            ->add('billingSameAsShipping', 'checkbox', array(
                'data' => false,
                'label'    => 'Billing address same as shipping address?',
                'required' => false,
            ));
        
        if ($this->order->isCustomOrder()) {
            $builder
                ->add('deposit', 'text', array('required' => false))
                ->add('payDeposit', 'checkbox', array(
                    'data' => false,
                    'label'    => 'I want to pay a deposit',
                    'required' => false,
                ));
        }
        
        if ($this->user->getPrimaryShippingAddress() !== NULL) {
            $builder
                ->add('setNewShipping', 'checkbox', array(
                    'label'    => 'Set a new shipping address?',
                    'required' => false,
                    ))
                ->add('setShippingPrimary', 'checkbox', array(
                    'label'    => 'Set this shipping address as your primary shipping address?',
                    'required' => false,
                ));
        }
        
        if ($this->user->getPrimaryBillingAddress() !== NULL) {
            $builder
                ->add('setNewBilling', 'checkbox', array(
                    'label'    => 'Set a new billing address?',
                    'required' => false,
                    ))
                ->add('setBillingPrimary', 'checkbox', array(
                    'label'    => 'Set this billing address as your primary billing address?',
                    'required' => false,
                ));
        }
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'saman_shipping_form';
    }
}