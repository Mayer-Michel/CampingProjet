<?php

namespace App\Controller;

use App\Repository\HebergementRepository;
use App\Repository\TypeRepository;
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
    #[Route('/', name: 'app_home')]
    public function index(HebergementRepository $hebergementRepository, TypeRepository $typeRepository): Response
    {
        //on va déclarer une variable
        $title = "Tous les hebergements";
        //on recupère les datas de tous les hebergements
        $hebergements = $hebergementRepository->findAll();
        //on recupère les datas de tous les types
        $types = $typeRepository->findAll(); 

        
        return $this->render('home/index.html.twig', [
            'title' => $title,
            'hebergements' => $hebergements,
            'types' => $types
        ]);
    }
}
