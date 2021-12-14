<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        if($this->getUser())
        {
            return $this->redirectToRoute('blog');
        }


        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'userRegistration' => true // ici on précise dans quelle condition on entre dans la classe RegistrationFormType pour afficher un formulaire en particulier, la classe contient plusieurs formulaire
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on fais appel à l'objet $userPasswordHasher de l'interface UserPasswordHasherInterface afin d'encoder le mot de passe en BDD
            // En argument on lui fournit l'objet entité dans lequel nous allons encoder un élément $user et on lui fournit le mot de passe saisie dans le formulaire
            
            $hash = $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
               
            );
            $user->setPassword($hash);

            $this->addFlash('success',"Félicitations ! Vous êtes maintenant inscrit !");

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /*
        Exo : Créer une page profil affichant les données utilisateurs authentifié:
        1. Créer une nouvelle route /profil
        2. Créer une nouvelle méthode userProfil()
        3. Cette méthode renvoie un template 'registration/profil.html.twig'
        4. Afficher dans ce template les informations de l'utilisateurs connecté
    */
    #[Route('/profil', name: 'profil')]
    public function userProfil() : Response
    {
        if(!$this->getUser())
        {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/profil.html.twig');
    }

    #[Route('/profil/{id}/edit', name: 'profil_edit')]
    public function editProfil(User $user, Request $request, EntityManagerInterface $manager) : Response
    {
        // dd($user);
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'userUpdate' => true // ici on précise dans quelle condition on entre dans la classe RegistrationFormType pour afficher un formulaire en particulier, la classe contient plusieurs formulaire
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success',"Vous avez modifié vos informations, merci de vous authentifié à nouveau");
            // une fois que l'utilisateur a modifié ses info de profil, on le redirige vers la page de deconnexion, on le deconnecter pour qu'il puisse après mettre a jour la session en s'authentifiant de nouveau
            return $this->redirectToRoute("app_logout");
        }

        return $this->render('registration/profil_edit.html.twig',[
            'updateForm' => $form->createView(),
        ]);
    }
}
