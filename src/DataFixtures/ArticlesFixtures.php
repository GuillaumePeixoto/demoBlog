<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticlesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //la boucle tourne 10 fois afin de créer 10 articles FICTIFS dans la BDD
        for($i = 1; $i <= 10; $i++)
        {
            // Pour insérer des données dans la table SQL Article, nous sommes obligé de passer par sa classe Entity correspondant 'App\Entity\Article', cette classe est le reflet de la table
            // SQL, elle contient des propriété identique aux champs/colonnes de la table SQL

            $article = new Article;
        
            // On éxecute tout les setters de l'objet afin de remplir les objets et d'ajouter un titre, contenu, image etc pour nos 10 articles

            $article->setTitre("Titre de l'article N°$i");
            $article->setContenu("<p> Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolorum mollitia repellendus fuga non sequi repudiandae optio? Fuga asperiores totam repudiandae.</p>");
            $article->setPhoto("https://picsum.photos/id/239/300/600");
            $article->setDate(new \DateTime());

            // Nous faisons à l'objet $manager ObjectManager
            // Une classe permet entre autre de manipuler les lignes SQL de la BDD (Insert, Update, Delete)
            // persist() : méthode issue de la classe ObjectManager (ORM Doctrine) permettant de garder en mémoire les 10 objet $articles et de préparer les requetes SQL
            $manager->persist($article);
            // $r = $bdd->prepare($article->getTitre(),$article->getContenu(),etc...);
        }

        // $product = new Product();
        // $manager->persist($product);


        // flush() : méthode issue de la classe ObjectManager (ORM Doctrine ) permettant véritablement d'éxecuter les requetes SQL en BDD
        $manager->flush();
    }
}
