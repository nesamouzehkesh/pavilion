<?php

namespace UserBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * 
 */
class MyProfileType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User',
        ));
    }
    
    /**
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text')
            ->add('firstName', 'text')
            ->add('lastName', 'text')
            ->add('currentPassword', 'password', array(
                'mapped' => false, 
                'required' => false
                ))
            ->add('password', 'password', array(
                'mapped' => false, 
                'required' => false
                ))
            ->add('rePassword', 'password', array(
                'mapped' => false, 
                'required' => false
                ))
            ->add('changePassword', 'checkbox', array(
                'mapped' => false,
                'data' => false,
                'label'    => 'I want to change the password',
                'required' => false,
            ));            
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'saman_user_form';
    }
}