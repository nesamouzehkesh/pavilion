<?php

namespace Library\Form;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * 
 */
class FormFieldType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('key', 'text')
            ->add('type', 'choice', array(
                'choices'  => array(
                    'text' => 'Text',
                    'textarea' => 'Textarea',
                    'number' => 'Number',
                    'choice' => 'Choice'
                    )
                ))
            ->add('label', 'text')
            ->add('required', 'checkbox', array('label' => 'Is required?', 'required' => false))
            ->add('visible', 'checkbox', array('label' => 'Is visible?', 'required' => false))
            ->add('choices', 'text', array('required' => false))
            ->add('default', 'text', array('required' => false));
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Library\Components\FormField',
        ));
    }
    
    /**
     * 
     * @return string
     */
    public function getName()
    {
        return 'saman_form_field_form';
    }
}