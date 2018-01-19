<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Form\Constraints;

use Symfony\Component\Validator\Constraint;
use Symphonia\CoreBundle\Form\Validators\UserConstraintValidator;

class UserConstraint extends Constraint
{
    public $message = "User does not exist";

    public function __construct($message = null)
    {
        if($message) {
            $this->message = $message;
        }
    }

    public function validatedBy()
    {
        return UserConstraintValidator::class;
    }
}
