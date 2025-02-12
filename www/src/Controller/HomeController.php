<?php

namespace App\Controller;

use App\Entity\Hebergement;
use App\Repository\HebergementRepository;
use App\Repository\TypeRepository;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    

     /**
     * Méthode qui retourne la page d'accueil avec tous les jeux
     * @Route("/", name="app_home")
     * @param HebergementRepository $hebergementRepository
     * @return Response
     */
    #[Route('/', name: 'app_home')]
    public function index(HebergementRepository $hebergementRepository): Response
    {
        //on va déclarer une variable
        $title = "Tous les hebergements";
        //on recupère les datas de tous les hebergements
        $hebergements = $hebergementRepository->findAll();

        
        return $this->render('home/index.html.twig', [
            'title' => $title,
        ]);
    }

    #[Route('/hebergements', name: 'all_hebergement')]
    public function All(HebergementRepository $hebergementRepository): Response
    {
        $hebergements = $hebergementRepository->findAll();

        return $this->render('hebergements/index.html.twig', [
            'hebergements' => $hebergements
        ]);
    }

    #[Route('/hebergements/filter', name: 'by_date')]
    public function filterByDate(Request $request, HebergementRepository $repo)
    {
        $dateStart = new \DateTime($request->query->get('date_start'));
        $dateEnd = new \DateTime($request->query->get('date_end'));

        $hebergements = $repo->findAvailableHebergements($dateStart, $dateEnd);

        return $this->render('hebergements/index.html.twig', [
            'hebergements' => $hebergements
        ]);
    }

    #[Route('/heberegments/detail/{id}', name: 'app_detail')]
    public function hebergementDetail(HebergementRepository $repo, int $id)
    {
        // On recupére l'hebergement avec les données
        $hebergements = $repo->hebergementDetail($id);

        $equipements = $repo->equipementByHeberg($id);

        $images = $repo->imageByHeberg($id);
    

        return $this->render('hebergements/detail.html.twig', [
            'hebergement' => $hebergements,
            'equipements' => $equipements,
            'images' => $images
        ]);
    }
}
