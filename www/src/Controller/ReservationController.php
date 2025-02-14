<?php

namespace App\Controller;

use App\Entity\Rental;
use App\Form\ReservationType;
use App\Repository\HebergementRepository;
use App\Repository\TarifRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class ReservationController extends AbstractController
{
    /**
     * Méthode qui retourne la page de recherche des hebergements
     * @Route("/resvation/search", name="app_reservation_search")
     * @param Request $request, SessionInterface $session
     * @return Response
     */
    #[Route('/reservation/search', name: 'app_reservation_search', methods: ['GET', 'POST'])]
    public function searchForm(Request $request, SessionInterface $session): Response
    {
        $form = $this->createForm(ReservationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Save form data in session to pass it to the next controller
            $session->set('reservation_data', $form->getData());

            // Redirect to the result page
            return $this->redirectToRoute('app_reservation_results');
        }

        return $this->render('reservation/reservation_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Méthode qui retourne les resultats
     * @Route("/resvation/results", name="app_reservation_results")
     * @param SessionInterface $session, HebergementRepository $hebergementRepository, TarifRepository $tarifRepo
     * @return Response
     */
    #[Route('/reservation/results', name: 'app_reservation_results', methods: ['GET'])]
    public function showResults(SessionInterface $session, HebergementRepository $hebergementRepository, TarifRepository $tarifRepo): Response
    {
        // Retrieve form data from session
        $data = $session->get('reservation_data');

        if (!$data) {
            // Handle the case where no form data is available (e.g., user accessed the results page directly)
            return $this->redirectToRoute('app_reservation_search');
        }

        $dateStart = $data['dateStart'];
        $dateEnd = $data['dateEnd'];
        $type = $data['type'];
        $adults = $data['adults'];
        $kids = $data['kids'];

        // Query available hebergements based on form data
        $availableHebergements = $hebergementRepository->findAvailableHebergements(
            $dateStart,
            $dateEnd,
            $type,
            $adults,
            $kids
        );

        // Convert the string dates to DateTime objects
        $dateStartObj = $dateStart;
        $dateEndObj = $dateEnd;

        // Calculate the number of nights
        $interval = $dateStartObj->diff($dateEndObj);
        $numberOfNights = $interval->days;


        $hebergementTarifs = [];
        $hebergementTotal = [];
        foreach ($availableHebergements as $hebergement) {
            $tarifs = $tarifRepo->getHebergementTarifByDate($hebergement['id'], $dateStart, $dateEnd);

            if (!empty($tarifs)) {
                $tarif = $tarifs[0];
                $hebergementTarifs[$hebergement['id']] = $tarif; // Assuming you get one tariff
                $hebergementTotal[$hebergement['id']] = $numberOfNights * $tarif['prix'];
            } else {
                $hebergementTarifs[$hebergement['id']] = null;
                $hebergementTotal[$hebergement['id']] = 0;
            }
        }

        // Return the result to the Twig template
        return $this->render('reservation/reservation_results.html.twig', [
            'availableHebergements' => $availableHebergements,
            'hebergementTarifs' => $hebergementTarifs, // Pass tariffs for each hebergement
            'hebergementTotal' => $hebergementTotal,
        ]);
    }


    /**
     * Méthode qui retourne la page detail avec les prix
     * @Route("/resvation/results/detail/{id}", name="app_reservation_detail")
     * @param SessionInterface $session, HebergementRepository $hebergementRepository, TarifRepository $tarifRepo, int $id
     * @return Response
     */
    #[Route('/reservation/results/detail/{id}', name: 'app_reservation_detail', methods: ['GET', 'POST'])]
    public function detailResults(SessionInterface $session, HebergementRepository $hebergementRepository, TarifRepository $tarifRepo, int $id): Response
    {

        // On recupére l'hebergement avec les données
        $hebergements = $hebergementRepository->hebergementDetail($id);
        $equipements = $hebergementRepository->equipementByHeberg($id);
        $types = $hebergementRepository->typeByHeberg($id);

        // Retrieve form data from session
        $data = $session->get('reservation_data');

        if (!$data) {
            // Handle the case where no form data is available (e.g., user accessed the results page directly)
            return $this->redirectToRoute('app_reservation_search');
        }

        $dateStart = $data['dateStart'];
        $dateEnd = $data['dateEnd'];
        $type = $data['type'];
        $adults = $data['adults'];
        $kids = $data['kids'];

        // Query available hebergements based on form data
        $availableHebergements = $hebergementRepository->findAvailableHebergements(
            $dateStart,
            $dateEnd,
            $type,
            $adults,
            $kids
        );

        // Convert the string dates to DateTime objects
        $dateStartObj = $dateStart;
        $dateEndObj = $dateEnd;

        // Calculate the number of nights
        $interval = $dateStartObj->diff($dateEndObj);
        $numberOfNights = $interval->days;


        $hebergementTarifs = [];
        $hebergementTotal = [];
        foreach ($availableHebergements as $hebergement) {
            $tarifs = $tarifRepo->getHebergementTarifByDate($hebergement['id'], $dateStart, $dateEnd);

            if (!empty($tarifs)) {
                $tarif = $tarifs[0];
                $hebergementTarifs[$hebergement['id']] = $tarif; // Assuming you get one tariff
                $hebergementTotal[$hebergement['id']] = $numberOfNights * $tarif['prix'];
            } else {
                $hebergementTarifs[$hebergement['id']] = null;
                $hebergementTotal[$hebergement['id']] = 0;
            }
        }

        // Return the result to the Twig template
        return $this->render('hebergements/detail.html.twig', [
            'hebergement' => $hebergements,
            'equipements' => $equipements,
            'hebergementTarifs' => $hebergementTarifs, // Pass tariffs for each hebergement
            'hebergementTotal' => $hebergementTotal,
            'types' => $types,
        ]);
    }


    /**
     * Méthode qui comfirme la reservation et le stocker dans la database
     * @Route("/resvation/confirm/{id}", name="app_reservation_confirm")
     * @param int $id, SessionInterface $session, HebergementRepository $hebergementRepo, TarifRepository $tarifRepo, EntityManagerInterface $entityManager, Security $security
     * @return Response
     */
    #[Route('/reservation/confirm/{id}', name: 'app_reservation_confirm')]
    public function confirmReservation(int $id, SessionInterface $session, HebergementRepository $hebergementRepo, TarifRepository $tarifRepo, EntityManagerInterface $entityManager, Security $security): Response
    {
        // Retrieve form data from session
        $data = $session->get('reservation_data');

        if (!$data) {
            return $this->redirectToRoute('app_reservation_search');
        }

        $user = $security->getUser(); // Get the authenticated user

        $dateStart = $data['dateStart'];
        $dateEnd = $data['dateEnd'];

        $interval = $dateStart->diff($dateEnd);
        $numberOfNights = $interval->days;

        // Get Hebergement
        $hebergement = $hebergementRepo->find($id);
        if (!$hebergement) {
            throw $this->createNotFoundException("Hébergement non trouvé");
        }

        // Get Tariff
        $tarifs = $tarifRepo->getHebergementTarifByDate($id, $data['dateStart'], $data['dateEnd']);
        if (empty($tarifs)) {
            return $this->redirectToRoute('app_reservation_results', ['error' => 'Tarif non disponible']);
        }

        $pricePerNight = $tarifs[0]['prix'];
        $totalPrice = $numberOfNights * $pricePerNight;

        // Retrieve number of adults and children
        $nbrAdult = $data['adults'];
        $nbrChildren = $data['kids'];

        // Create Rental Entry
        $rental = new Rental();
        $rental->setUser($user);
        $rental->setHebergement($hebergement);
        $rental->setDateStart($dateStart);
        $rental->setDateEnd($dateEnd);
        $rental->setPrixTotal($totalPrice);
        $rental->setNbrAdult($nbrAdult);
        $rental->setNbrChildren($nbrChildren);

        $entityManager->persist($rental);
        $entityManager->flush();

        // Redirect to confirmation page
        return $this->redirectToRoute('app_reservation_success');
    }

    /**
     * Méthode qui retourne la page de confirmation de reservation 
     * @Route("/resvation/success", name="app_reservation_success")
     * @param
     * @return Response
     */
    #[Route('/reservation/success', name: 'app_reservation_success')]
    public function reservationSuccess(): Response
    {
        return $this->render('reservation/reservation_success.html.twig', [
            'message' => 'Votre réservation a été enregistrée avec succès !'
        ]);
    }
}
