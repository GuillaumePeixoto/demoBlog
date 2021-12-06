<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        // méthode rendu : en fonction de la route l'URL, la méthode render() envoi un template, un rendu sur le navigateur
        return $this->render('blog/home.html.twig',[
            'title' => 'Bienvenue sur le blog Symfony',
            'age' => 25
        ]);
    }

    #[Route('/blog', name: 'blog')]
    public function blog(ArticleRepository $repoArticle): Response
    {
        /*
            Injection de dépendances : c'est un des fondement de Symfony, ici notre méthode dépend de la classe ArticleRepository pour fonctionner correctement
            Ici Symfony comprend que la méthode blog() attend un argument objet issu de la classe ArticleRepository, automatiquement Symfony envoi une instance de cette classe en argument de cette classe
            $repoArticle est un objet issu de la classe ArticleRepository

            Symfony est une application qui est capable de répondre à un navigateur lorseque celui-ci appel une adresse (ex: localhost:8000/blog), le controller doit etre capable d'envoyer
            un rendu, un template sur le navigateur

            Ici, lorseque l'on transmet la route '/blog' dans l'URL, cela execute la méthode index() dans le controller qui renvoie le template '/blog/index.html.twig' sur le navigateur
        
            Pour selectionner des données en BDD, nous devons passer par une class Repository, ses classes permettant uniquement d'executer des requetes de selection SELECT en BDD, contient des
            méthodes mise à dispositions par Symfony (findAll(), find(), findBy(), etc...)

            Ici nous devons importer au sein de notre controller la classe Article Repository pour pouvoir selectionner dans la table Article
            $repoArticle est un objet issu de la classe ArticleRepository
            getRepository() est une méthode issue de l'objet Doctrine permettant ici d'importer la classe ArticleRepository
        */

        //$repoArticle = $doctrine->getRepository(Article::class);

        // dump() / dd() : outil de débug de symfony
        //dd($repoArticle);


        // findAll() : méthode issue de la classe ArticleRepository permettant de selectionner l'ensemble de la table SQL et de récuperer un talbeau multi contenant l'ensemble des articles
        $articles = $repoArticle->findAll(); // SELECT * FROM article + FETCH_ALL
        // dump($articles);

        // dd($articles);

        return $this->render('blog/blog.html.twig',[
            'articles' => $articles
        ]);
    }

    #[Route('/blog/new', name: 'blog_create')]
    #[Route('/blog/{id}/edit', name: 'blog_edit')]
    public function blogCreate(Article $article = null, Request $request, EntityManagerInterface $manager): Response
    {
        // Si les données dans le tableau ARRAY $_POST sont supérieur à 0 alors on entre dans la condition
        // if($request->request->count() > 0)
        // {


        //     // Pour insérer dans la table SQL 'article', nous avons besoin d'un objet de son entité correspondante
        //     $article = new Article;
        //     $article->setTitre($request->request->get('titre'));
        //     $article->setContenu($request->request->get('contenu'));
        //     $article->setPhoto($request->request->get('photo'));
        //     $article->setDate(new \DateTime());

        //     // dd($article);

        //     $manager->persist($article);

        //     $manager->flush();
        // }

        if(!$article)
        {
            $article = new Article;            
        }

        // $article->setTitre("Titre nul");
        // $article->setContenu("Contenu null");


        $formArticle = $this->createForm(ArticleType::class, $article);

        $formArticle->handleRequest($request);

        if($formArticle->isSubmitted() && $formArticle->isValid())
        {
            // Le champs date n'existe pas en tant que formulaire
            $article->setDate(new \DateTime());

            //dd($article);

            $manager->persist($article);

            $manager->flush();
        }

        return $this->render('blog/blog_create.html.twig',[
            'formArticle' => $formArticle->createView() // on transmet le formulaire au tempalte afin de pouvoir l'afficher en twig
            // createView() : Retourne un petit objet qui représente
        ]);
    }
    
    // Méthode permettant d'afficher le détail d'un article
    // On définit une route 'paramètrée' {id}, ici la route permet de recevoir l'id d'un article stocké en BDD
    #[Route('/blog/{id}', name: 'blog_show')]
    public function blogShow(Article $article) : Response
    {
        /*
            Ici, nous envoyons un ID dans l'url et nous imposons en argument un objet issu de l'entité Article onc la table SQL
            Donc Symfony est capable de selectionner en BDD l'article en fonction de l'id passé dans l'url et de l'envoyé automatiquement en argument de la méthode blogShow() dans la variable de reception $article

        */

        // $repoArticle = $doctrine->getRepository(Article::class);

        // L'id transmit dans la route est transmit automatiquement en argument de la méhode blogShow($id) dans la variable de réception $id
        // dd($id);
        return $this->render('blog/blog_show.html.twig',[
            'article' => $article
        ]);
    }
}
