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
    private $param = array(
        'contentStructure' => array(
            'content' => array(
                'type' => 'text',
                'label' => 'Content',
                'default' => '',
                'required' => true
                )
        )
    );
    
    /**
     * 
     * @param array $param
     */
    public function __construct(array $param = null)
    {
        if (null !== $param) {
            $this->param['specificationFields'] = $param;
        }
    }
    
    /**
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text')
            ->add('specifications', 'saman_collection', array(
                'fields' => $this->param['specificationFields'],
                ))
            ->add('image', 'saman_media', array(
                'attr'  => array('isMultiple' => false, 'dragover' => false)
                ))
            ->add('images', 'saman_media')
            ->add('description', 'textarea')
            ->add('price', 'number')
            ->add('originalPrice', 'number')
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