<?php

namespace OC\PlatformBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\AdvertEditType;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
      /*$content = $this
            ->get('templating')
            ->render('OCPlatformBundle:Advert:index.html.twig', array('nom'=> 'Lionel'));

            return new Response($content);
       */

        $em = $this->getDoctrine()->getManager();
        $listAdverts = $em->getRepository('OCPlatformBundle:Advert')->getAdverts($page, 3);

        $totalPages = ceil(count($listAdverts)/3);

        if($page < 1) {
          throw new NotFoundHttpException('Page"'.$page.'" inexistante.');
        }

        return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
          'listAdverts' => $listAdverts,
          'totalPages'  => $totalPages,
          'page'        => $page
        ));
    }


    public function viewAction($id)
    {

        //$url= $this->get('router')->generate('oc_platform_home');
        //return new RedirectResponse($url);

        //$tag= $request->query->get('tag');

        //return new Response(
        //	"Affichage de l'annonce d'id :".$id." , avec le tag: ".$tag);

        /*$advert = array(
            'title' => 'Recherche développeur Symfony2',
            'id' => $id,
            'author' => 'Lionel',
            'content' => 'Nous recherchons un développeur Symfony2 débutant sur Valenciennes',
            'date' => new \Datetime()
          );*/


      $em = $this->getDoctrine()->getManager();

      $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

      if(null === $advert) {
        throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas");
      }

    	$listApplication = $em->getRepository('OCPlatformBundle:Application')->findBy(array('advert'=>$advert));

      // On récupère maintenant la liste des advertSkills
      $listAdvertSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')->findBy(array('advert' => $advert));


      return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
        'advert'           => $advert,
        'listApplications' => $listApplication,
        'listAdvertSkills' => $listAdvertSkills
      )); // ou utiliser render à la place de renderResponse


      /*$response = new Response(json_encode(array('id'=>$id)));
      $response->headers->set('Content-Type', 'application/json');
      return $response;*/

      /*$session = $request->getSession();
      $userId= $session->get('user_id');
      $session->set('user_id', 91);

      return new Response("<body>Je suis une page de test, je n'ai rien à dire</body>");
    */
    }
    

    public function addAction(Request $request)
    {
      // if (!$this->get('security.authorization_checker')->isGranted('ROLE_AUTEUR')) {
      //   throw new AccessDeniedException('Accès limité aux auteurs.');
      // }

     //  $em= $this->getDoctrine()->getManager();

      $advert = new Advert();
      /*	$advert->setTitle('Recherche développeur Symfony');
        $advert->setAuthor('Lionel');
        $advert->setContent('Nous recherchons un développeur Symfony débutant sur Lille');
        $advert->setAuthoremail('auteur@mail.com');


        $image= new Image();
        $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
        $image->setAlt('Job de rêve');

        $advert->setImage($image);

        $application1 = new Application();
        $application1->setAuthor('Marine');
        $application1->setContent("Je suis très motivée");

        $application2 = new Application();
        $application2->setAuthor('Pierrot');
        $application2->setContent("J'aime la prog");

        $application1->setAdvert($advert);
        $application2->setAdvert($advert);



        // on récupère toutes les compétiences possibles
        $listSkills = $em->getRepository('OCPlatformBundle:Skill')->findAll();

        // Pour chaque compétence
        foreach ($listSkills as $skill) {
          // On crée une nouvelle <<relation entre 1 annonce et 1 compétence >>
          $advertSkill = new AdvertSkill();

          // On la lie à l'annonce, qui est ici toujours la même
          $advertSkill->setAdvert($advert);
          // On la lie à la compétence, qui change ici dans la boucle foreach
          $advertSkill->setSkill($skill);

          // Arbitrairement, on dit que chaque compétence est requise au niveau expert
          $advertSkill->setLevel('Expert');

          // Et bien sur , on persiste cette entité de relation, propriétaire des deux autres relations
          $em->persist($advertSkill);
        }

        // on "persiste" l'entité
        $em->persist($advert);

        $em->persist($application1);
        $em->persist($application2);

        // on "flush" ce qui a été fait avant
        $em->flush();

        if($request->isMethod('POST')){

        $request->getSession()->getFlashBag()->add('Notice', 'Annonce bien enregistrée');

        return $this->redirectToRoute('oc_platform_view', array('id'=> $advert->getId()));

        }

        $antispam = $this->container->get('oc_platform.antispam');

        /*$text = '...';
        if ($antispam->isSpam($text)) {
          throw new \Exception('Votre message a été détecté comme spam!!');
        }*/

      $form = $this->get('form.factory')->create(AdvertType::class, $advert);

      //  $form = $formBuilder->getForm();

      if($request->isMethod('POST'))
      {
        $form->handleRequest($request);

        if($form->isValid())
        {
          $em = $this->getDoctrine()->getManager();
          $em->persist($advert);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

          return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }
      }

      return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
        //'advert'=>$advert,
        'form' => $form->createView(),
      ));


      /*$session = $request->getSession();

      $session->getFlashBag()->add('info', 'Annonce bien enregistrée');

      $session->getFlashBag()->add('info', 'oui oui elle est bien enregistrée');

      return $this->redirectToRoute('oc_platform_view', array('id'=> 5)); */
    }


    public function editAction($id, Request $request)
    {

      $em = $this->getDoctrine()->getManager();

      $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

      if(null === $advert){
        throw new NotFoundHttpException("l'annonce d'id ".$id." n'existe pas.");
      }

      //la méthode findAll retourne toutes les catégories de la bdd
      // $listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();
      //
      // //on boucle sur les catégories pour les lier à l'annonce
      // foreach($listCategories as $category) {
      //   $advert->addCategory($category);
      // }

      //pour persister le changement dans la relation, il faut persister l'entité propriétaire
      //ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupéré depuis Doctrine

      //étape 2: on déclenche l'enregistrement

      // $em->flush();

      $form = $this->get('form.factory')->create(AdvertEditType::class, $advert);


      if ($request->isMethod('POST'))
      {
        $form->handleRequest($request);

        if($form->isValid())
        {
            $em->persist($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée');

            return $this->redirectToRoute('oc_platform_view', array('id'=> $advert->getId()));
        }
      }


      // $advert = array(
      // 		'title' => 'Recherche développeur Symfony2',
      // 		'id' => $id,
      // 		'author' => 'Lionel',
      // 		'content' => 'Nous recherchons un développeur Symfony2 débutant sur Valenciennes',
      // 		'date' => new \Datetime()
      // 	);
      return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
        'advert'=> $advert,
        'form'  => $form->createView()
        ));

    }


    public function deleteAction($id, Request $request)
    {
      $em = $this->getDoctrine()->getManager();

      $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
      $listSkills = $em->getRepository('OCPlatformBundle:AdvertSkill')->findBy(
        array(
          'advert' => $advert->getId()
        )
      );

      if ( null === $advert) {
       throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
      }

      $form = $this->get('form.factory')->create();

      if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
      {

        foreach($advert->getCategories() as $category){
        $advert->removeCategory($category);
        }

        foreach ($advert->getApplications() as $application) {
          $em->remove($application);
        }

        foreach ($listSkills as $skill) {
          $em->remove($skill);
        }

        $em->remove($advert);

        $em->flush();

        $request->getSession()->getFlashBag()->add('notice', 'Annonce bien supprimée !');

        return $this->redirectToRoute('oc_platform_home');
      }


      return $this->render('OCPlatformBundle:Advert:delete.html.twig', array(
        'advert' => $advert,
        'form'   => $form->createView()
      ));
    }




    public function viewSlugAction($slug, $year, $_format)
    {
      return new Response(
          "On pourrait afficher l'annonce correspondant au
          slug '".$slug."', créée en ".$year." et au format ".$_format."."
      );
    }

    public function menuAction()
    {
      $em = $this->getDoctrine()->getManager();
      $listAdverts = $em->getRepository('OCPlatformBundle:Advert')->findBy(
        array('published' => 1),
        array('date' => 'desc'),
        3,
        0
      );
      // $listAdverts = array(
      // 		array('id' => 2, 'title' => 'Recherche développeur Symfony'),
      // 		array('id' => 5, 'title' => 'Mission de Webmaster'),
      // 		array('id' => 9, 'title' => 'Offre de stage Webdesigner')
      // );

      return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
          'listAdverts'=>$listAdverts
        ));
    }

}
