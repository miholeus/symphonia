<?php

namespace Soulex\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecurityController extends Controller
{
    public function loginAction()
    {
        return $this->render('SoulexCoreBundle:security:login.html.twig');
    }
}
