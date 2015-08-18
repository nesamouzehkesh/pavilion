<?php

namespace Saman\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

class MediaType extends AbstractType
{
    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'saman_media';
    }
}