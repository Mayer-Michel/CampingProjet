<?php

namespace App\Controller;

use App\Form\ReservationType;
use App\Repository\HebergementRepository;
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
    public function showResults(SessionInterface $session, HebergementRepository $hebergementRepository): Response
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

        // Return the result to the Twig template
        return $this->render('home/reservation_results.html.twig', [
            'availableHebergements' => $availableHebergements,
        ]);
    }
}
