<?php

namespace Saman\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AceType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        
    }

    public function getParent()
    {
        return 'textarea';
    }

    public function getName()
    {
        return 'saman_ace';
    }
}