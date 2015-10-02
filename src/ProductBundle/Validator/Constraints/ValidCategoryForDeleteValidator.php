<?php

namespace ProductBundle\Validator\Constraints;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use ProductBundle\Entity\Category;
/**
 *
 * Class ValidCategoryForDeleteValidator
 * @package ProductBundle\Validator\Constraints
 */
class ValidCategoryForDeleteValidator extends ConstraintValidator
{
    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Category $category
     * @param Constraint $constraint
     */
    public function validate($category, Constraint $constraint)
    {
        // Detect any Active Appraisl Templates using this Goal
        /** @var AppraisalRepository $repo */
        $countCategoryProducts = Category::getRepository($this->em)
            ->countCategoryProducts($category->getId());

        // Should be no matches
        if ($countCategoryProducts > 0) {
            $this->context->addViolation($constraint->message);
        }
    }
}