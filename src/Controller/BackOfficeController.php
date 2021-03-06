<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Commentaire;
use App\Entity\User;
use App\Form\ArticleType;
use App\Form\CategoryType;
use App\Form\CommentaireType;
use App\Form\RegistrationFormType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentaireRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    #[Route('/admin/article/{id}/remove', name: 'app_admin_article_remove')]
    public function adminArticles(EntityManagerInterface $manager, ArticleRepository $repoArticle, Article $artRemove = null): Response
    {

        $colonnes = $manager->getClassMetadata(Article::class)->getFieldNames();
        $articles = $repoArticle->findAll();

        if($artRemove)
        {
            $id = $artRemove->getId();

            $manager->remove($artRemove);
            $manager->flush();

            $this->addFlash('success', "L'article n°$id a été supprimer avec succès !");
            return $this->redirectToRoute('app_admin_articles');
        }

        //dd($articles);

        return $this->render('back_office/admin_articles.html.twig',[
            'colonnes' => $colonnes,
            'articles' => $articles
        ]);
    }

    #[Route('/admin/article/add', name: 'app_admin_article_add')]
    #[Route('/admin/article/{id}/modify', name: 'app_admin_article_modify')]
    public function adminArticleForm(Article $article = null, EntityManagerInterface $manager, Request $request, SluggerInterface $slugger): Response
    {
        if($article)
        {
            $photoBdd = $article->getPhoto();
            $action = "Modification";
        }
        else
        {
            $article = new Article;
            $action = "Ajout";
        }
        $articleForm = $this->createForm(ArticleType::class, $article);

        $articleForm->handleRequest($request);

        if($articleForm->isSubmitted() && $articleForm->isValid())
        {
            if(!$article->getId())
            {
                $article->setDate(new \DateTime());

            }

            $photo = $articleForm->get('photo')->getData();

            if($photo)
            {
                $nomOriginePhoto = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                //dd($nomOriginePhoto);

                // cela est necessaire pour inclure en toute sécurité le nom du fichier dans l'URL
                $secureNomPhoto = $slugger->slug($nomOriginePhoto);

                $nouveauNomFichier = $secureNomPhoto.' - '.uniqid().'.'.$photo->guessExtension();
                // dd($nouveauNomFichier);
                try
                {
                    $photo->move(
                        $this->getParameter('photo_directory'),
                        $nouveauNomFichier
                    );
                }
                catch(FileException $e)
                {

                }

                $article->setPhoto($nouveauNomFichier);

            }
            else
            {
                if(isset($photoBdd))
                {
                    $article->setPhoto($photoBdd);                    
                }
                else
                {
                    $article->setPhoto(null);
                }

            }

            $this->addFlash('success', "$action de l'article !");

            $manager->persist($article);

            $manager->flush();

            return $this->redirectToRoute('app_admin_articles');
        }

        return $this->render('back_office/admin_article_form.html.twig',[
            'articleForm' => $articleForm->createView(),
            'action' => $action,
            'havePhoto' => $article->getPhoto()
        ]);
    }

    #[Route('/admin/category', name: 'app_admin_categorys')]
    #[Route('/admin/category/{id}/remove', name: 'app_admin_category_remove')]
    public function adminCategory(EntityManagerInterface $manager, CategoryRepository $repoCategory, Category $category = null): Response
    {

        $colonnes = $manager->getClassMetadata(Category::class)->getFieldNames();
        $categorys = $repoCategory->findAll();


        if($category)
        {
            $titre = $category->getTitre();

            if(count($category->getArticles()) == 0)
            {
                $manager->remove($category);
                $manager->flush();
                $this->addFlash('success', "La categorie $titre a été supprimer avec succès !");
            }
            else
            {
                $this->addFlash('danger', "Vous ne pouvez pas supprimer cette catégorie tant qu'il reste des articles lié à celle-ci");
            }



            return $this->redirectToRoute('app_admin_categorys');
        }

        return $this->render('back_office/admin_category.html.twig',[
            'colonnes' => $colonnes,
            'categorys' => $categorys
        ]);
    }


    #[Route('/admin/category/add', name: 'app_admin_category_add')]
    #[Route('/admin/category/{id}/modify', name: 'app_admin_add_category')]
    public function adminCategoryForm(Category $category = null, EntityManagerInterface $manager, Request $request): Response
    {
        if($category)
        {
            $action = "Modification";
        }
        else
        {
            $category = new Category;
            $action = "Ajout";
        }

        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);

        if($categoryForm->isSubmitted() && $categoryForm->isValid())
        {
            $this->addFlash('success', "$action de la categorie !");

            $manager->persist($category);

            $manager->flush();

            return $this->redirectToRoute('app_admin_categorys');
        }

        return $this->render('back_office/admin_category_form.html.twig',[
            'categoryForm' => $categoryForm->createView(),
            'action' => $action,
        ]);
    }

    #[Route('/admin/commentaire', name: 'app_admin_comments')]
    #[Route('/admin/commentaire/{id}/remove', name: 'app_admin_comment_remove')]
    public function adminComment(EntityManagerInterface $manager, CommentaireRepository $repoComment, Commentaire $commentaire = null): Response
    {

        $colonnes = $manager->getClassMetadata(Commentaire::class)->getFieldNames();
        $comments = $repoComment->findAll();

        if($commentaire)
        {
            $id = $commentaire->getId();

            $manager->remove($commentaire);
            $manager->flush();
            $this->addFlash('success', "Le commentaire n°$id a été supprimer avec succès !");

            return $this->redirectToRoute('app_admin_comments');
        }

        return $this->render('back_office/admin_comment.html.twig',[
            'colonnes' => $colonnes,
            'comments' => $comments
        ]);
    }

    #[Route('/admin/commentaire/{id}/modify', name: 'app_admin_modify_comment')]
    public function adminCommentForm(Commentaire $commentaire, EntityManagerInterface $manager, Request $request): Response
    {

        $commentaireForm = $this->createForm(CommentaireType::class, $commentaire);

        $commentaireForm->handleRequest($request);

        if($commentaireForm->isSubmitted() && $commentaireForm->isValid())
        {
            $manager->persist($commentaire);

            $manager->flush();

            $this->addFlash('success', "Modification du commentaire !");

            return $this->redirectToRoute('app_admin_comments');
        }

        return $this->render('back_office/admin_comment_form.html.twig',[
            'commentaireForm' => $commentaireForm->createView()
        ]);
    }

    
    #[Route('/admin/users', name: 'app_admin_users')]
    #[Route('/admin/user/{id}/remove', name: 'app_admin_user_remove')]
    public function adminUsers(EntityManagerInterface $manager, UserRepository $repoUser, User $user = null): Response
    {

        $colonnes = $manager->getClassMetadata(User::class)->getFieldNames();
        $users = $repoUser->findAll();

        if($user)
        {
            $id = $user->getId();

            $manager->remove($user);
            $manager->flush();
            $this->addFlash('success', "L'utilisateur n°$id a été supprimer avec succès !");

            return $this->redirectToRoute('app_admin_comments');
        }

        return $this->render('back_office/admin_users.html.twig',[
            'colonnes' => $colonnes,
            'users' => $users
        ]);
    }

    #[Route('/admin/user/{id}/modify', name: 'app_admin_mofify_user')]
    public function adminUserForm(User $user, EntityManagerInterface $manager, Request $request): Response
    {

        $colonnes = $manager->getClassMetadata(User::class)->getFieldNames();

        $userForm = $this->createForm(RegistrationFormType::class, $user, [
            'adminUserUpdate' => true
        ]);

        $userForm->handleRequest($request);

        if($userForm->isSubmitted() && $userForm->isValid())
        {
            $this->addFlash('success', "Modification du role de l'utilisateur n° !");

            $manager->persist($user);

            $manager->flush();

            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('back_office/admin_user_form.html.twig',[
            'userForm' => $userForm->createView(),
            'colonnes' => $colonnes,
            'user' => $user
        ]);
    }
}
