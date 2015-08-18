<?php

namespace Saman\PlayGroundBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SampleForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('select', 'saman_select2', array(
            //'mapped' => false,
            'label' => 'Saman Select 2',
            'class' => 'SamanLabelBundle:Label',
            'data' => array(1, 2),
            'minimumInputLength' => 0,
            'multiple' => true,
            'placeholder' => 'ss',
            'allowClear' => true,
            ));
        
        $builder->add('icon', 'saman_icon', array(
            //'mapped' => false,
            'label' => 'Saman Select 2',
            ));        
    
        $builder->add('labels', 'entity', array(
            'attr' => array('class' => 'form-control form-control-select2'),
            'empty_value' => 'Choose a label',
            'multiple'  => true,
            'class' => 'SamanLabelBundle:Label',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('l')
                    ->where('l.deleted = 0');
            }
        ));
        /* 
        $fields = array(
            'name' => array(
                'mapped' => false,
                'type' => 'saman_media',
                'label' => 'name',
                'attr' => array(
                    'isMultiple' => false,
                    'dragover' => true
                    ),
                'required' => true,
                'default' => null,
                'data' => null
            ),
        );
        
        $data = array(
            array(
                'name' => '',
            ),
        );
        
        $builder->add('multiple', 'saman_multiple', array(
            'mapped' => false,
            'fields' => $fields,
            'data' => $data
            ));
        */
    }

    public function getName()
    {
        return 'cityCountry';
    }
}