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
    private $type;
    
    public function __construct($type = null)
    {
        $this->type = $type;
    }

    /**
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ('editUser' === $this->type) {
            $builder
                ->add('firstName', 'text')
                ->add('lastName', 'text')
                ->add('password', 'password', array(
                    'mapped' => false, 
                    'required' => false))
                ->add('newPassword', 'password', array(
                    'mapped' => false, 
                    'required' => false))
                ->add('reNewPassword', 'password', array(
                    'mapped' => false, 
                    'required' => false))
                ->add('changePassword', 'checkbox', array(
                    'mapped' => false,
                    'data' => false,
                    'label'    => 'I want to change my password?',
                    'required' => false,
                ));
        } else {
            $builder
                ->add('email', 'email', array('mapped' => false))
                ->add('reEmail', 'email', array('mapped' => false))
                ->add('firstName', 'text')
                ->add('lastName', 'text')
                ->add('password', 'password');
        }
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