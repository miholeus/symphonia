<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symphonia\CoreBundle\Form\Constraints\UserConstraint;
use Symphonia\CoreBundle\Form\DataTransformers\EmailToUserTransformer;
use Symphonia\CoreBundle\Service\User;

class UserByEmailType extends AbstractType
{
    private $userService;

    public function __construct(User $userService)
    {
        $this->userService = $userService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EmailType::class, [
                'constraints' => [
                    new UserConstraint("User with that email does not exist")
                ],
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Email',
                    'autofocus' => true,
                    'required' => true
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Send code',
                'attr' => [
                    'class' => 'btn btn-primary btn-block btn-flat',
                ]
            ]);
        $builder->get('user')
            ->addModelTransformer(new EmailToUserTransformer($this->userService));
    }
}
