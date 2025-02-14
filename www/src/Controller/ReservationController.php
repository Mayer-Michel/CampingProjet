<?php

namespace App\Controller;

use App\Form\ReservationType;
use App\Repository\HebergementRepository;
use App\Repository\TarifRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

class ReservationController extends AbstractController
{
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

        return $this->render('home/reservation_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

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
        $hebergementTotals = [];
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
        return $this->render('home/reservation_results.html.twig', [
            'availableHebergements' => $availableHebergements,
            'hebergementTarifs' => $hebergementTarifs, // Pass tariffs for each hebergement
            'hebergementTotal' => $hebergementTotal,
        ]);
    }
}
