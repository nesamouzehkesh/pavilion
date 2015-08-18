<?php

namespace Saman\ShoppingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidProduct extends Constraint
{
    public $message = array(
        'PRODUCT_TITLE_IS_BLANK' => 'PRODUCT_TITLE_IS_BLANK',
    );
        
    public function validatedBy()
    {
        return get_class($this).'Validator';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}