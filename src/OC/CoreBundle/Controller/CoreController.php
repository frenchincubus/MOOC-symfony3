<?php

namespace OC\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;



class CoreController extends Controller
{

	public function indexAction($page){

		return $this->render('OCCoreBundle:Core:index.html.twig', array('listAdverts'=>array()
		));
	}


	public function contactAction(Request $request){

		$session = $request->getSession();
		$session->getFlashBag()->add('info', 'La page contact n\'est pas encore disponible, merci de revenir plus tard.');


		return $this->redirectToRoute('oc_core_homepage');
	}

}
