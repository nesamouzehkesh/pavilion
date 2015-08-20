<?php

namespace MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('title', 'text', array(
            'mapped' => false
            ));
        
        $builder->add('image', 'media', array(
            'mapped' => false
            ));
    }

    public function getName()
    {
        return 'photo';
    }
}