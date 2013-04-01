<?php

namespace DrawSmooth\SquaresInterfaceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LandingController extends Controller
{
    public function indexAction() {
    	$logger=$this->get('logger');


    	return $this->render('DrawSmoothSquaresInterfaceBundle:Landing:index.html.twig', array('name' => 'Test Three'));
    }
}
