<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(): Response
    {
       // $userFirstname = $session->get('user_firstname');
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
          //  'user_firstname' => $userFirstname,
        ]);
    }
}
