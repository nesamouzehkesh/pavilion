<?php

namespace Saman\AppearanceBundle\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ThemeRawStructureForm extends AbstractType
{
    /**
     * 
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Saman\AppearanceBundle\Entity\Theme',
        ));
    } 
    /**
     * 
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('isRawStructure', 'saman_toggle', array(
            'required' => false,
            'attr' => array('inline' => true)
            ));
        $builder->add('rawStructure', 'textarea');
    }

    public function getName()
    {
        return 'saman_appearance_theme_raw_structure_form';
    }
}