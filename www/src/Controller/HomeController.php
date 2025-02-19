<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\HebergementRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * Méthode qui retourne la page d'accueil avec tous les jeux
     * @Route("/", name="app_home")
     * @param HebergementRepository $hebergementRepository
     * @return Response
     */
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        //on va déclarer une variable
        $title = "Tous les hebergements";

        return $this->render('home/index.html.twig', [
            'title' => $title,
        ]);
    }

    /**
     * Méthode qui retourne le profile de l'utilisateur
     * @Route("/profile/{id}", name="app_user_profile")
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/profile/{id}', name: 'app_user_profile', methods: ['GET', 'POST'])]
    public function profile(Request $request, UserRepository $userRepo, EntityManagerInterface $entityManager, int $id): Response
    {
        // si l'id n'est pas celui de l'utilisateur connecté et si l'utilisateur n'est pas un admin 
        $user = $userRepo->find($id);
        if (!$user || ($user->getId() !== $this->getUser()->getId() && !$this->isGranted('ROLE_ADMIN'))) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(UserType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * Méthode qui retourne la liste des utilisateurs
     * @Route("/users", name="app_user_index")
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route('/profile/edit/{id}', name: 'app_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserRepository $userRepo, EntityManagerInterface $entityManager, int $id): Response
    {
        // si l'id n'est pas celui de l'utilisateur connecté et si l'utilisateur n'est pas un admin        
        $user = $userRepo->find($id);
        if (!$user || ($user->getId() !== $this->getUser()->getId() && !$this->isGranted('ROLE_ADMIN'))) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(UserType::class, $user, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
