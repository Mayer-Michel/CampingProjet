<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Rental;
use App\Form\ReservationType;
use App\Repository\TarifRepository;
use App\Repository\RentalRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\HebergementRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    #[Route('/reservation/confirm/{id}', name: 'app_reservation_confirm', methods: ['GET', 'POST'])]
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
        $tarifs = $tarifRepo->getHebergementTarifByDate($id, $dateStart, $dateEnd);
        if (empty($tarifs)) {
            return $this->redirectToRoute('app_reservation_results', ['error' => 'Tarif non disponible']);
        }

        $pricePerNight = $tarifs[0]['prix'];
        $totalPrice = $numberOfNights * $pricePerNight;

        // Retrieve number of adults and children
        $nbrAdult = $data['adults'];
        $nbrChildren = $data['kids'];

        // Default status to "En attente" (1) if not set in session
        $statu = $data['statu'] ?? 1;

        // Create Rental Entry
        $rental = new Rental();
        $rental->setUser($user);
        $rental->setHebergement($hebergement);
        $rental->setDateStart($dateStart);
        $rental->setDateEnd($dateEnd);
        $rental->setPrixTotal($totalPrice);
        $rental->setNbrAdult($nbrAdult);
        $rental->setNbrChildren($nbrChildren);
        $rental->setStatu($statu); // Default to "En attente"

        $entityManager->persist($rental);
        $entityManager->flush();

        return $this->redirectToRoute('app_reservation_success', [
            'id' => $rental->getId(),
        ]);
    }

    #[Route('/reservation/success/{id}', name: 'app_reservation_success', methods: ['GET', 'POST'])]
    public function reservationSuccess(int $id, RentalRepository $rentalRepo, Request $request, EntityManagerInterface $entityManager): Response
    {
        $rental = $rentalRepo->find($id);

        if (!$rental) {
            throw $this->createNotFoundException("Réservation non trouvée");
        }

        // Handle the form submission to validate or cancel the reservation
        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');

            if ($action === 'validate') {
                $rental->setStatu(2); // Set status to "Validée"
            } elseif ($action === 'cancel') {
                $rental->setStatu(3); // Set status to "Annulée"
            }
            // Save the changes to the database
            $entityManager->persist($rental);
            $entityManager->flush();

            // Redirect to a success page after status update
            return $this->redirectToRoute('app_reservation_history', [
                'id' => $rental->getUser()->getId(),
            ]);
        }

        return $this->render('reservation/reservation_success.html.twig', [
            'reservation' => $rental,
        ]);
    }

    #[Route('/reservation/history/{id}', name: 'app_reservation_history', methods: ['GET', 'POST'])]
    public function history(User $user, RentalRepository $rentalRepo): Response
    {
        // Fetch the user's reservation history
        $reservations = $rentalRepo->getUserReservationHistory($user);

        return $this->render('reservation/history.html.twig', [
            'user' => $user,
            'reservations' => $reservations,
        ]);
    }

    /**
     * @Route("/reservation/cancel/{id}", name="reservation_cancel")
     * 
     */
    #[Route('/reservation/cancel/{id}', name: 'reservation_cancel', methods: ['GET', 'POST'])]
    public function cancel(Request $request, Rental $rental, EntityManagerInterface $entityManager): Response
    {
        // Get the current date and the reservation's start date
        $now = new \DateTime();
        $dateStart = $rental->getDateStart();

        // Calculate the difference in seconds
        $daysDifference = $dateStart->getTimestamp() - $now->getTimestamp();

        // Check if the cancellation is allowed (at least 2 days in seconds)
        if ($daysDifference >= 172800) { // 172800 seconds = 2 days
            // If CSRF token is valid, proceed with cancellation
            if ($this->isCsrfTokenValid('cancel' . $rental->getId(), $request->request->get('_token'))) {
                // Change status to 3 (Annulée) instead of deleting
                $rental->setStatu(3);
                $entityManager->persist($rental);
                $entityManager->flush();

                $this->addFlash('success', 'Your reservation has been successfully cancelled.');
            } else {
                $this->addFlash('error', 'Invalid CSRF token.');
            }
        } else {
            $this->addFlash('error', 'You cannot cancel the reservation less than 2 days before the start date.');
        }

        // Redirect to the reservation history page
        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/reservation/confirm/{id}', name: 'app_reservation_confirm', methods: ['POST'])]
    public function statuReservation(int $id, RentalRepository $rentalRepo, EntityManagerInterface $entityManager, Security $security): Response
    {
        $rental = $rentalRepo->find($id);

        if (!$rental) {
            throw $this->createNotFoundException("Réservation non trouvée");
        }

        // Ensure the user is the one who made the reservation
        $user = $security->getUser();
        if ($rental->getUser() !== $user) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas modifier cette réservation.");
        }

        if ($rental->getStatu() == 1) {  // "En attente"
            $rental->setStatu(2); // Change status to "Confirmée"
            $entityManager->flush(); // Save changes to the database
        }

        // Redirect to the reservation history page
        return $this->redirectToRoute('app_reservation_history', ['id' => $user->getId()]);
    }

    #[Route('/reservation/cancel/{id}', name: 'reservation_cancel', methods: ['POST'])]
    public function cancelReservation(int $id, RentalRepository $rentalRepo, EntityManagerInterface $entityManager, Security $security): Response
    {
        $rental = $rentalRepo->find($id);

        if (!$rental) {
            throw $this->createNotFoundException("Réservation non trouvée");
        }

        // Ensure the user is the one who made the reservation
        $user = $security->getUser();
        if ($rental->getUser() !== $user) {
            throw $this->createAccessDeniedException("Vous ne pouvez pas annuler cette réservation.");
        }

        if ($rental->getStatu() == 1 || $rental->getStatu() == 2) {  // "En attente" or "Confirmée"
            $rental->setStatu(3); // Change status to "Annulée"
            $entityManager->flush(); // Save changes to the database
        }

        // Redirect to the reservation history page
        return $this->redirectToRoute('app_reservation_history', ['id' => $user->getId()]);
    }
}
