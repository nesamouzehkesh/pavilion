<?PHP

namespace Saman\CmsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ValidPageValidator extends ConstraintValidator
{
    /**
     * 
     * @param \Saman\CmsBundle\Entity\Page $page
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($page, Constraint $constraint)
    {
        if ('' === $page->getTitle()) {
            $this->context->addViolation(
                $constraint->message['PAGE_TITLE_IS_BLANK']
                );
        }
        
        if ('' === $page->getUrl()) {
            $this->context->addViolation(
                $constraint->message['PAGE_URL_IS_NOT_VALID']
                );
        }
    }
}