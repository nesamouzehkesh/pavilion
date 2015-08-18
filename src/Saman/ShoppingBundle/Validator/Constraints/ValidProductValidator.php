<?PHP

namespace Saman\ShoppingBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidProductValidator extends ConstraintValidator
{
    /**
     * 
     * @param \Saman\ShoppingBundle\Entity\Product $page
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($product, Constraint $constraint)
    {
        if ('' === $product->getTitle()) {
            $this->context->addViolation(
                $constraint->message['PRODUCT_TITLE_IS_BLANK']
                );
        }
    }
}