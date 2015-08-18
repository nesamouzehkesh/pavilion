<?php

namespace Saman\AppearanceBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Saman\Library\Service\Helper;
use Doctrine\ORM\EntityRepository;
/**
 * 
 */
class ThemeForm extends AbstractType
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
    public function __construct(Helper $helper, $parameters)
    {
        $this->helper = $helper;
        $this->helper->setParametrs($parameters);
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Saman\AppearanceBundle\Entity\Theme',
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
        $builder->add('template', 'choice', array(
            'choices'   => $this->getAllTemplatesName(),
        ));        
        
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
        return 'saman_appearance_theme_form';
    }
    
    /**
     * 
     * @return type
     */
    private function getAllTemplatesName()
    {
        $templatesArray = array();
        $templates = $this->helper->getParameter('templates');
        foreach ($templates as $key => $template) {
            $templatesArray[$key] = $template['title'];
        }
        
        return $templatesArray;
    }
}