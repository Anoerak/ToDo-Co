<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{

    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    #[Route('/users', name: 'app_users_list', methods: ['GET'])]
    public function usersListAction(EntityManagerInterface $emi): Response
    {
        return $this->render('user/list.html.twig', [
            'controller_name' => 'UserController',
            'users' => $emi->getRepository(User::class)->findAll()
        ]);
    }



    #[Route('/users/create', name: 'app_user_create', methods: ['POST'])]
    public function userCreateAction(EntityManagerInterface $emi, Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $password = $this->encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password)
                ->setRoles(['ROLE_USER']);

            $emi->persist($user);
            $emi->flush();

            $this->addFlash('success', 'L\'utilisateur a bien été ajouté.');

            // if connected user is admin, redirect to admin page
            return $this->isGranted('ROLE_ADMIN') ? $this->redirectToRoute('app_admin') : $this->redirectToRoute('app_login');
        }

        return $this->render('user/create.html.twig', [
            'controller_name' => 'UserController',
            'form' => $form->createView(),
        ]);
    }



    #[Route('/users/{id}/edit', name: 'app_user_edit', methods: ['PUT'])]
    public function userEditAction(User $user, Request $request, EntityManagerInterface $emi): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page.');
            return $this->redirectToRoute('app_homepage');
        } else {
            if (!$this->isGranted('ROLE_ADMIN') &&  $user != $this->getUser()) {
                $this->addFlash('danger', 'Vous n\'avez pas les droits pour accéder à cette page.');
                return $this->redirectToRoute('app_homepage');
            }

            $form = $this->createForm(UserType::class, $user);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if (!empty($form->get('username')->getData())) {
                    $user->setUsername($form->get('username')->getData());
                }

                if (!empty($form->get('email')->getData())) {
                    $user->setEmail($form->get('email')->getData());
                }

                if (!empty($form->get('password')->getData())) {
                    $password = $this->encoder->hashPassword($user, $user->getPassword());
                    $user->setPassword($password);
                }

                if (!$form->get('roles')->getData()) {
                    $user->setRoles(['ROLE_USER']);
                }

                $emi->flush();

                $this->addFlash('success', 'L\'utilisateur a bien été modifié. Veuillez vous reconnecter.');

                return $this->isGranted('ROLE_ADMIN') ? $this->redirectToRoute('app_admin') : $this->redirectToRoute('app_login');
            }

            return $this->render('user/edit.html.twig', [
                'controller_name' => 'UserController',
                'form' => $form->createView(),
                'user' => $user
            ]);
        }
    }

    #[Route('/users/{id}/delete', name: 'app_user_delete', methods: ['DELETE'])]
    public function userDeleteAction(User $user, EntityManagerInterface $emi): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour supprimer un utilisateur.');
            return $this->redirectToRoute('app_homepage');
        }

        $emi->remove($user);
        $emi->flush();

        $this->addFlash('success', 'L\'utilisateur a bien été supprimé.');

        return $this->redirectToRoute('app_admin');
    }
}
