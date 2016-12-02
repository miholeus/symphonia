<?php

namespace Soulex\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('soulex_core_homepage');
        }

        $form = $this->createForm('Soulex\CoreBundle\Form\Login');

        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form->handleRequest($request);

        return $this->render('SoulexCoreBundle:security:login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }
}
