<?php

namespace MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ToggleType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
    }

    public function getParent()
    {
        return 'checkbox';
    }

    public function getName()
    {
        return 'saman_toggle';
    }
}