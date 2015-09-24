<?php

namespace ShoppingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use UserBundle\Form\AddressType;
use UserBundle\Entity\User;

/**
 * 
 */
class ShippingAddressType extends AbstractType
{
    private $user;
        
    /**
     * @param User $user
     */
    public function __construct(User $user = null)
    {
        $this->user = $user;
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