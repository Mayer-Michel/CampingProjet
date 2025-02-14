<?php

namespace App\Controller;


use App\Repository\HebergementRepository;
use App\Repository\TarifRepository;
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
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        //on va déclarer une variable
        $title = "Tous les hebergements";

        return $this->render('home/index.html.twig', [
            'title' => $title,
        ]);
    }


    #[Route('/hebergements', name: 'all_hebergement')]
    public function all(HebergementRepository $hebergementRepository): Response
    {
        $hebergements = $hebergementRepository->findAll();

        return $this->render('hebergements/index.html.twig', [
            'hebergements' => $hebergements
        ]);
    }

    #[Route('/hebergements/detail/{id}', name: 'app_detail')]
    public function hebergementDetail(HebergementRepository $repo, TarifRepository $tarif, int $id)
    {
        // On recupére l'hebergement avec les données
        $hebergements = $repo->hebergementDetail($id);

        $equipements = $repo->equipementByHeberg($id);

        // $saisons = $tarif->getTarifBySaison($id);

        $prices = $repo->getHebergementTarif($id);


        return $this->render('hebergements/detail.html.twig', [
            'hebergement' => $hebergements,
            'prices' => $prices,
            'equipements' => $equipements,
            // 'saison' => $saisons
        ]);
    }

    #[Route('/reservation', name: 'app_reservation', methods: ['GET'])]
    public function reservation(TypeRepository $typeRepository): Response
    {
        $typeHeber = $typeRepository->findAll();

        return $this->render('home/reservation.html.twig', [
            'typeHeber' => $typeHeber,
            'hebergements' => [] // Initially empty
        ]);
    }

}
