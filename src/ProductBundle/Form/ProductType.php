<?php

namespace ProductBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * 
 */
class ProductType extends AbstractType
{
    /**
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('description', 'textarea')
            ->add('price', 'number')
            ->add('available', 'choice', array(
                'choices'  => array('1' => 'Active', '0' => 'Inactive')
                ));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProductBundle\Entity\Product',
        ));
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'saman_product_form';
    }
}