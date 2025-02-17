<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\HebergementRepository;
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
    public function profile(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
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
    

    // #[Route('/hebergements', name: 'all_hebergement')]
    // public function all(HebergementRepository $hebergementRepository): Response
    // {
    //     $hebergements = $hebergementRepository->findAll();

    //     return $this->render('hebergements/index.html.twig', [
    //         'hebergements' => $hebergements
    //     ]);
    // }
}
