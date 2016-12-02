<?php
/**
 * @package    Soulex\CoreBundle
 * @author     miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @version    $Id: $
 */

namespace Soulex\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class Login extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Email / Login',
                    'autofocus' => true
                ]
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Password'
                ]
            ])
            ->add('remember_me', CheckboxType::class, [
                'label' => 'Запомнить меня',
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Войти',
                'attr' => [
                    'class' => 'btn btn-primary btn-block btn-flat',
                ]
            ]);
    }
}