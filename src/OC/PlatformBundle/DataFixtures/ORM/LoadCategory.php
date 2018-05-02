<?php 
// src/OC/PlatformBundle/DataFixtures/ORM/LoadCategory.php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Category;

class LoadCategory implements FixtureInterface
{
    //Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
      //liste des noms de catégorie à ajouter
      $names = array(
	'Développement web',
	'Développement mobile',
	'Graphisme',
	'Intégration',
	'Réseau'
       );

      foreach ($names as $name) {
	//on crée la catégorie
	$category = new Category();
	$category->setName($name);

	// on la persiste
	$manager->persist($category);

      }
    
    //on déclenche l'enregistrement de toutes les catégories
    $manager->flush();
    }

}
