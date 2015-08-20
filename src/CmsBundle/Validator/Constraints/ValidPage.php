<?php

namespace CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidPage extends Constraint
{
    public $message = array(
        'PAGE_TITLE_IS_BLANK' => 'PAGE_TITLE_IS_BLANK',
        'PAGE_URL_IS_NOT_VALID' => 'PAGE_URL_IS_NOT_VALID',
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