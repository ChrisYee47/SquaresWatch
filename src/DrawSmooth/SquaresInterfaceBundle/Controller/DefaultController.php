<?php

namespace DrawSmooth\SquaresInterfaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('DrawSmoothSquaresInterfaceBundle:Default:index.html.twig', array('name' => $name));
    }
}
