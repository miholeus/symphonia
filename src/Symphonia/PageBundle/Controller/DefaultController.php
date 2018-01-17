<?php

namespace Symphonia\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('SymphoniaPageBundle:Default:index.html.twig');
    }
}
