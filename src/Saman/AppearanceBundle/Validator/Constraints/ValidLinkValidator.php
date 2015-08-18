<?PHP

namespace Saman\AppearanceBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Saman\AppearanceBundle\Entity\Link;

class ValidLinkValidator extends ConstraintValidator
{
    /**
     * 
     * @param \Saman\AppearanceBundle\Entity\Link $link
     * @param \Symfony\Component\Validator\Constraint $constraint
     */
    public function validate($link, Constraint $constraint)
    {
        if (null === $link->getTitle()) {
            $this->context->addViolation(
                $constraint->message['LINK_TITLE_IS_BLANK']
                );
        }
        
        if (null === $link->getUrl() && null === $link->getPage()) {
            $this->context->addViolation(
                $constraint->message['LINK_URL_IS_NOT_VALID']
                );
        }
    }
}