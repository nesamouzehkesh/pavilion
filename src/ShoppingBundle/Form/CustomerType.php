<?php

namespace ShoppingBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * 
 */
class CustomerType extends AbstractType
{
    /**
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'text', array('mapped' => false))
            ->add('reEmail', 'text', array('mapped' => false))
            ->add('firstName', 'text')
            ->add('lastName', 'text')
            ->add('password', 'password');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User',
            'validation_groups' => array('registration'),
        ));
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'saman_customer_register_form';
    }
}