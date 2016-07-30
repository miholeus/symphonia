<?php

namespace Soulex\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SoulexCoreBundle:Default:index.html.twig');
    }
}
