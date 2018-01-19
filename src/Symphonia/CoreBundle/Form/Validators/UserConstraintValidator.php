<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Form\Validators;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symphonia\CoreBundle\Entity\User;

class UserConstraintValidator extends ConstraintValidator
{
    /**
     * Validates value to be user instance
     *
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if(!($value instanceof User)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
