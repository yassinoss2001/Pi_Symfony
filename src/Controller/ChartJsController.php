<?php

namespace App\Controller;
use App\Entity\Restaurant;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ChartJsController extends AbstractController
{
    #[Route('/stat', name: 'app_stat')]
    public function index(RestaurantRepository $restaurantRepository, ChartBuilderInterface $chartBuilder): Response
    {
        // Récupérer les restaurants depuis le repository
        $restaurants = $restaurantRepository->findAll();

        // Initialiser les tableaux pour les labels et les données
        $labels = [];
        $data = [];

        // Parcourir les restaurants et extraire les informations pertinentes
        foreach ($restaurants as $restaurant) {
            $labels[] = $restaurant->getNom(); // Utilisez le nom du restaurant comme label
            $data[] = $restaurant->getRate(); // Utilisez le taux du restaurant comme donnée
        }

        // Créer un graphique de type bar
        $chart = $chartBuilder->createChart(Chart::TYPE_PIE);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Taux des restaurants',
                    'backgroundColor' => 'rgb(81, 152, 255)',
                    'borderColor' => 'rgb(0, 168, 255)',
                    'data' => $data,
                ],
            ],
        ]);

        // Définir les options du graphique
        $chart->setOptions([]);

        // Rendre la vue avec le graphique
        return $this->render('chart_js/index.html.twig', [
            'controller_name' => 'ChartJsController',
            'chart' => $chart,
        ]);
    }
}