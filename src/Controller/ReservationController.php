<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Evennemnt;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Transport; 
use Symfony\Component\Mailer\Mailer; 

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();
    
            // Send email confirmation
            $this->sendReservationConfirmationEmail($reservation, $mailer);
    
            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }
    
    private function sendReservationConfirmationEmail(Reservation $reservation, MailerInterface $mailer)
    {
        $transport = Transport::fromDsn('gmail+smtp://iyedtlili19%40gmail.com:yqqd%20fusy%20iqef%20qfvp@smtp.gmail.com');
        $mailer = new Mailer($transport);
        $email = (new Email())
            ->from('iyedtlili19@gmail.com') // Update with your email address
            ->to($reservation->getEmailContact())
            ->subject('Reservation Confirmation')
            ->text('Your reservation has been accepted. We look forward to seeing you at the event.');
    
        $mailer->send($email);
    }
    #[Route('/list', name: 'app_reservation_list', methods: ['GET'])]
    public function listReservations(EntityManagerInterface $entityManager): Response
    {
        // Fetch all reservations from the database
        $reservations = $entityManager->getRepository(Reservation::class)->findAll();

        return $this->render('reservation/indexf.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/newf/{id}', name: 'app_reservation_form', methods: ['GET', 'POST'])]
    public function newf($id, Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
{
    // Retrieve the event entity based on the provided ID
    $event = $entityManager->getRepository(Evennemnt::class)->find($id);

    // Check if the event exists
    if (!$event) {
        throw $this->createNotFoundException('Event not found');
    }

    // Create a new reservation instance
    $reservation = new Reservation();

    // Set the event ID for the reservation
    $reservation->setEvennementId($event);

    // Create the reservation form using the ReservationType form type class
    $form = $this->createForm(ReservationType::class, $reservation);
    $form->handleRequest($request);

    // Handle form submission
    if ($form->isSubmitted() && $form->isValid()) {
        // Persist the reservation entity
        $entityManager->persist($reservation);
        $entityManager->flush();

        // Send email notification
        $email = (new Email())
            ->from('iyedtlili19@gmail.com')
            ->to($reservation->getEmailContact())
            ->subject('Reservation Confirmation')
            ->text('Your reservation has been accepted. We look forward to seeing you at the event.');

        $mailer->send($email);

        // Redirect to the reservation index page or wherever appropriate
        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }

    // Render the reservation form template
    return $this->render('reservation/newf.html.twig', [
        'reservation' => $reservation,
        'form' => $form->createView(),
    ]);
}


    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
