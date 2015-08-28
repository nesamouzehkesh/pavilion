<?php

namespace Library\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
* @Annotation
* @Target({"PROPERTY"})
*/
final class MediaAnnotation extends Annotation
{
    public $type;

    public function single()
    {
        return $this->type == 'single' ? true : false;
    }

    public function multiple()
    {
        return $this->type == 'multiple' ? true : false;
    }
}