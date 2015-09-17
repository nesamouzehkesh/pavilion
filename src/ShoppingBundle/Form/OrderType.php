<?php

namespace ShoppingBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * 
 */
class OrderType extends AbstractType
{
    private $orderConfig;
    
    public function __construct($orderConfig)
    {
        $this->orderConfig = $orderConfig;
    }
    /**
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'saman_collection', array(
                'fields' => $this->orderConfig['orderStructure'],
                //'data' => $row['settings']
                ))
            ->add('attachments', 'saman_media');
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ShoppingBundle\Entity\Order',
        ));
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'saman_order_form';
    }
}