<?php

namespace Symphonia\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symphonia\CoreBundle\Form\ChangePasswordType;
use Symphonia\CoreBundle\Form\UserByEmailType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('symphonia_core_homepage');
        }

        $form = $this->createForm('Symphonia\CoreBundle\Form\Login');

        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form->handleRequest($request);

        return $this->render('SymphoniaCoreBundle:security:login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * Sends message to user with instructions to recover password
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function recoverySendMessageAction(Request $request)
    {
        /** @var Form $form */
        $form = $this->createForm(UserByEmailType::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            /** @var \Symphonia\CoreBundle\Entity\User $user */
            $user = $form->get('user')->getData();
            $service = $this->get('user.service');
            $recoveryCode = $this->get('security.email_password_recovery')
                ->sendPasswordRecoveryCode($user);
            $user->setVerifyEmailUuid($recoveryCode);
            $service->save($user);

            return $this->render('SymphoniaCoreBundle:security/recovery:code_sent.html.twig', [
                'email' => $user->getEmail()
            ]);
        }
        return $this->render('SymphoniaCoreBundle:security/recovery:recover.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Updates user's password by code
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function recoveryChangePasswordAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('symphonia_core_homepage');
        }
        /** @var Form $form */
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);
        $service = $this->get('user.service');
        if ($form->isValid()) {
            /** @var \Symphonia\CoreBundle\Entity\User $user */
            $user = $this->get('user.service')
                ->findByRecoveryCode($form->get('code')->getData());
            if ($user) {
                $service->changePassword(
                    $user,
                    $form->get('password')->getData()
                );
                $message = [
                    'header' => 'Password recovery',
                    'text' => 'Password was successfully changed.',
                    'type' => 'login'
                ];
            } else {
                $message = [
                    'header' => 'Password recovery error',
                    'text' => 'Invalid recovery code.',
                    'type' => 'recover'
                ];
            }
        } else {
            $user = $this->get('user.service')
                ->findByRecoveryCode($request->get('code'));

            if ($user) {
                return $this->render('SymphoniaCoreBundle:security/recovery:change_password.html.twig', [
                    'form' => $form->createView(),
                    'code' => $request->get('code')
                ]);
            } else {
                $message = [
                    'header' => 'Password recovery error',
                    'text' => 'Invalid recovery code.',
                    'type' => 'recover'
                ];
            }
        }
        return $this->render('SymphoniaCoreBundle:security/recovery:message.html.twig', [
            'message' => $message
        ]);
    }
}
