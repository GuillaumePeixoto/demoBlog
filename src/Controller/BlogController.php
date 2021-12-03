<?php

namespace App\Controller;

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
    public function blog(): Response
    {
        /*
            Symfony est une application qui est capable de répondre à un navigateur lorseque celui-ci appel une adresse (ex: localhost:8000/blog), le controller doit etre capable d'envoyer
            un rendu, un template sur le navigateur

            Ici, lorseque l'on transmet la route '/blog' dans l'URL, cela execute la méthode index() dans le controller qui renvoie le template '/blog/index.html.twig' sur le navigateur
        */

        return $this->render('blog/blog.html.twig');
    }

    #[Route('/blog/12', name: 'blog_show')]
    public function blogShow() : Response
    {
        return $this->render('blog/blog_show.html.twig');
    }
}
