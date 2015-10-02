<?php

namespace ProductBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class ValidCategoryForDelete extends Constraint
{
    public $message = 'This category can not be removed. There are some products using this category';

    public function validatedBy()
    {
        return 'validator.product.category.for.delete';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}