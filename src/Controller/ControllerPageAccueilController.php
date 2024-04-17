<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ControllerPageAccueilController extends AbstractController
{
    #[Route('/accueil', name: 'app_controller_page_accueil')]
    public function index(): Response
    {
        return $this->render('controller_page_accueil/index.html.twig');
    }
}
