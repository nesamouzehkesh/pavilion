<?php

namespace MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class IconType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'saman_icon';
    }
}