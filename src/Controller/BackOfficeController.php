<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackOfficeController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('back_office/index.html.twig', [
            'controller_name' => 'BackOfficeController',
        ]);
    }

    #[Route('/admin/articles', name: 'app_admin_articles')]
    public function adminArticles(EntityManagerInterface $manager, ArticleRepository $repoArticle): Response
    {
        $colonnes = $manager->getClassMetadata(Article::class)->getFieldNames();
        $articles = $repoArticle->findAll();
        //dd($articles);

        /*
            Exo : 
            Afficher l'ensemble des articles
            Dans le template 'admin_articles' mettre en forme l'affichage
            Afficher le nom de la catégorie de chaque article
            Afficher le nombre de commentaire
            Prévoir un lien de modif

        */

        return $this->render('back_office/admin_articles.html.twig',[
            'colonnes' => $colonnes,
            'articles' => $articles
        ]);
    }
}
