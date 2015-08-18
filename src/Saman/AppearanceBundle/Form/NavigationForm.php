<?php

namespace Saman\AppearanceBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Saman\Library\Service\Helper;

/**
 * 
 */
class NavigationForm extends AbstractType
{
    /**
     *
     * @var Helper $helper
     */
    private $helper;
    
    /**
     * 
     * @param type $parameters
     */
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Saman\AppearanceBundle\Entity\Navigation',
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
        $builder->add('description', 'textarea');
    }

    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'saman_appearance_navigation_form';
    }
}