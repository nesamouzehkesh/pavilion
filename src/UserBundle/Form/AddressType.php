<?php

namespace UserBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use UserBundle\Entity\User;

/**
 * 
 */
class AddressType extends AbstractType
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
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$this->user instanceof User) {
            $builder
                ->add('fullName', 'text', array('required' => false));
        } else {
            $builder
                ->add('fullName', 'text', array('data' => $this->user->getName(), 'required' => false));
        }
        
        $builder
            ->add('firstAddressLine', 'text', array('required' => false))
            ->add('secondAddressLine', 'text', array('required' => false))
            ->add('city', 'text', array('required' => false))
            ->add('state', 'text', array('required' => false))
            ->add('postCode', 'text', array('required' => false))
            ->add('country', 'country', array('required' => false))
            ->add('phoneNumber', 'text', array('required' => false));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\Address',
        ));
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'saman_address_form';
    }
}