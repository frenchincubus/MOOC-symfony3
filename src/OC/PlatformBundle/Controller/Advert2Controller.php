<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class Advert2Controller extends Controller
{
    public function indexAction()
    {
	$content = $this
	->get('templating')
	->render('OCPlatformBundle:Advert:index2.html.twig', array('nom'=> 'Lionel'));

        return new Response($content);
    }
}


