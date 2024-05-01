<?php

namespace App\Controller;

use App\Entity\Evennemnt;
use App\Form\EvennemntType;
use App\Repository\EvennemntRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/evennemnt')]
class EvennemntController extends AbstractController
{
    #[Route('/', name: 'app_evennemnt_index', methods: ['GET'])]
    public function index(EvennemntRepository $evennemntRepository): Response
    {
        return $this->render('evennemnt/index.html.twig', [
            'evennemnts' => $evennemntRepository->findAll(),
        ]);
    } 
    
    #[Route('/liste_evennements_client', name: 'app_evennemnt_liste_evennements_client')]
public function listeEvennementsClient(Request $request, EvennemntRepository $evennemntRepository, PaginatorInterface $paginator): Response
{
    // Retrieve all events from the repository
    $allEvennemntsQuery = $evennemntRepository->findAll();

    // Paginate the query result
    $evennemnts = $paginator->paginate(
        $allEvennemntsQuery, // Query to paginate
        $request->query->getInt('page', 1), // Current page number
        2 // Number of items per page
    );
    return $this->render('evennemnt/ListeEvennemntClient.html.twig', [
        'evennemnts' => $evennemnts,
    ]);
}



    #[Route('/calendar', name: 'app_evennemnt_calendar')]
    public function calendar(EvennemntRepository $evennemntRepository): Response
    {
        $evennemnts = $evennemntRepository->findAll(); // Fetch all Evennemnt entities from the repository
    
        return $this->render('evennemnt/calendar.html.twig', [
            'evennemnts' => $evennemnts, // Pass the Evennemnt entities to the template
        ]);
    }
    


    #[Route('/new', name: 'app_evennemnt_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evennemnt = new Evennemnt();
        $form = $this->createForm(EvennemntType::class, $evennemnt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image_path')->getData();
            if ($imageFile) {
                $imageFileName = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $imageFileName
                );
                $evennemnt->setImage_Path($imageFileName);
            }
            $entityManager->persist($evennemnt);
            $entityManager->flush();

            return $this->redirectToRoute('app_evennemnt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evennemnt/new.html.twig', [
            'evennemnt' => $evennemnt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evennemnt_show', methods: ['GET'])]
    public function show(Evennemnt $evennemnt): Response
    {
        return $this->render('evennemnt/show.html.twig', [
            'evennemnt' => $evennemnt,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_evennemnt_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Evennemnt $evennemnt, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvennemntType::class, $evennemnt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_evennemnt_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('evennemnt/edit.html.twig', [
            'evennemnt' => $evennemnt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_evennemnt_delete', methods: ['POST'])]
    public function delete(Request $request, Evennemnt $evennemnt, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evennemnt->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evennemnt);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_evennemnt_index', [], Response::HTTP_SEE_OTHER);
    }
}
