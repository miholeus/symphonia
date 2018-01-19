<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Form\DataTransformers;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symphonia\CoreBundle\Service\User;

class EmailToUserTransformer implements DataTransformerInterface
{
    private $userService;

    public function __construct(User $userService)
    {
        $this->userService = $userService;
    }


    public function transform($value)
    {
        if(null === $value) {
            return '';
        } else if($value instanceof \Symphonia\CoreBundle\Entity\User) {
            return $value->getEmail();
        } else {
            throw new TransformationFailedException();
        }
    }

    public function reverseTransform($value)
    {
        return $this->userService->findByEmail($value);
    }

}