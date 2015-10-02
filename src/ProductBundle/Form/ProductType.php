<?php

namespace ProductBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
            ->add('image', 'saman_media', array(
                'attr'  => array('isMultiple' => false, 'dragover' => false)
                ))
            ->add('images', 'saman_media')
            ->add('description', 'textarea')
            ->add('price', 'number')
            ->add('available', 'choice', array(
                'choices'  => array('1' => 'Active', '0' => 'Inactive')
                ));
        
        $builder->add('categories', 'entity', array(
            'required' => false,
            'attr' => array('class' => 'form-control form-control-select2'),
            'empty_value' => 'Choose a label',
            'multiple'  => true,
            'class' => 'ProductBundle:Category',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('c')
                    ->where('c.deleted = 0');
            }
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