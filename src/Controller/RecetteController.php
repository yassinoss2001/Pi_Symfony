<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/recette')]
class RecetteController extends AbstractController
{
    #[Route('/', name: 'app_recette_index', methods: ['GET'])]
    public function index(RecetteRepository $recetteRepository): Response
    {
        return $this->render('recette/index.html.twig', [
            'recettes' => $recetteRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_recette_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $recette = new Recette();
        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            $videoFile = $form->get('video')->getData();
                if ($imageFile) {
                $imageFileName = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $imageFileName
                );
                $recette->setImage($imageFileName);
            }
                if ($videoFile) {
                $videoFileName = uniqid().'.'.$videoFile->guessExtension();
                $videoFile->move(
                    $this->getParameter('videos_directory'),
                    $videoFileName
                );
                $recette->setVideo($videoFileName);
            }
    
            $entityManager->persist($recette);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->renderForm('recette/new.html.twig', [
            'recette' => $recette,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_recette_show', methods: ['GET'])]
    public function show(Recette $recette): Response
    {
        return $this->render('recette/show.html.twig', [
            'recette' => $recette,
        ]);
    }
#[Route('/{id}/edit', name: 'app_recette_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Recette $recette, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(RecetteType::class, $recette);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Check if the 'image' field is empty
        $existingImage = $recette->getImage();
        $newImage = $form->get('image')->getData();

        if (!$newImage) {
            // If no new image is provided, keep the existing one
            $recette->setImage($existingImage);
        } else {
            // If a new image is provided, handle it as in the 'new' action
            $imageFileName = uniqid().'.'.$newImage->guessExtension();
            $newImage->move(
                $this->getParameter('images_directory'),
                $imageFileName
            );
            $recette->setImage($imageFileName);
        }

        // Check if the 'video' field is empty
        $existingVideo = $recette->getVideo();
        $newVideo = $form->get('video')->getData();

        if (!$newVideo) {
            // If no new video is provided, keep the existing one
            $recette->setVideo($existingVideo);
        } else {
            // If a new video is provided, handle it similarly to the image
            $videoFileName = uniqid().'.'.$newVideo->guessExtension();
            $newVideo->move(
                $this->getParameter('videos_directory'),
                $videoFileName
            );
            $recette->setVideo($videoFileName);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('recette/edit.html.twig', [
        'recette' => $recette,
        'form' => $form,
    ]);
}


    #[Route('/{id}', name: 'app_recette_delete', methods: ['POST'])]
    public function delete(Request $request, Recette $recette, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$recette->getId(), $request->request->get('_token'))) {
            $entityManager->remove($recette);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
    }
}
