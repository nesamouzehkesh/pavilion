<?php

namespace Saman\ShoppingBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Saman\Library\Service\Helper;
use Doctrine\ORM\EntityRepository;
/**
 * 
 */
class ProductForm extends AbstractType
{
    /**
     *
     * @var Helper $helper
     */
    private $helper;
    
    /**
     *
     * @var type 
     */
    protected $parameters;
    
    /**
     * 
     * @param type $parameters
     */
    public function __construct(Helper $helper, $parameters = array())
    {
        $this->helper = $helper;
        $this->parameters = $parameters;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Saman\ShoppingBundle\Entity\Product',
        ));
    }    

    /**
     * 
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', 'text');
        $builder->add('image', 'saman_media');
        $builder->add('content', 'textarea');//ckeditor
        
        $builder->add('labels', 'entity', array(
            'attr' => array('class' => 'form-control form-control-select2'),
            'empty_value' => 'Choose a label',
            'multiple'  => true,
            'class' => 'SamanLabelBundle:Label',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('l');
            }
        ));
            
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'saman_shopping_product_form';
    }
}