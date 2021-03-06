<?php

namespace Symphonia\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SymphoniaCoreBundle:default:index.html.twig');
    }

    public function headerAction()
    {
        return $this->render('@SymphoniaCore/default/header.html.twig');
    }
}
