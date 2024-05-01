<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

 


#[Route('/menu')]
class MenuController extends AbstractController
{
    #[Route('/', name: 'app_menu_index', methods: ['GET'])]
    public function index(MenuRepository $menuRepository): Response
    {
        return $this->render('menu/index.html.twig', [
            'menus' => $menuRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_menu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
       
                if ($imageFile) {
                $imageFileName = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $imageFileName
                );
                $menu->setImage($imageFileName);
            }
            $entityManager->persist($menu);
            $entityManager->flush();

            return $this->redirectToRoute('app_menu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('menu/new.html.twig', [
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_menu_show', methods: ['GET'])]
    public function show(Menu $menu): Response
    {
        return $this->render('menu/show.html.twig', [
            'menu' => $menu,
            
        ]);
    }

    
    #[Route('/{id}/edit', name: 'app_menu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Menu $menu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingImage = $menu->getImage();
        $newImage = $form->get('image')->getData();

        if (!$newImage) {
            // If no new image is provided, keep the existing one
            $menu->setImage($existingImage);
        } else {
            // If a new image is provided, handle it as in the 'new' action
            $imageFileName = uniqid().'.'.$newImage->guessExtension();
            $newImage->move(
                $this->getParameter('images_directory'),
                $imageFileName
            );
            $menu->setImage($imageFileName);
        }
            $entityManager->flush();

            return $this->redirectToRoute('app_menu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('menu/edit.html.twig', [
            'menu' => $menu,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_menu_delete', methods: ['POST'])]
    public function delete(Request $request, Menu $menu, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$menu->getId(), $request->request->get('_token'))) {
            $entityManager->remove($menu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_menu_index', [], Response::HTTP_SEE_OTHER);
    }
}
