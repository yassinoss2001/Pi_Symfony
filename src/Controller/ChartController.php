<?php
namespace App\Controller;

use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class ChartController extends AbstractController
{
    #[Route('/chart', name: 'chart')]
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        // Récupération du nombre de restaurants par place
        $restaurants = $restaurantRepository->findAll();
        $placesCount = [];

        foreach ($restaurants as $restaurant) {
            $place = $restaurant->getPlace();
            if (!isset($placesCount[$place])) {
                $placesCount[$place] = 1;
            } else {
                $placesCount[$place]++;
            }
        }

        // Création du graphique à secteurs
        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable([
            ['Place', 'Nombre de restaurants'],
            // Convertir les données en un tableau associatif pour le graphique
            ...array_map(fn ($place, $count) => [$place, $count], array_keys($placesCount), $placesCount)
        ]);

        // Configuration du graphique
        $pieChart->getOptions()->setTitle('Répartition des restaurants par place');
        $pieChart->getOptions()->setHeight(400);
        $pieChart->getOptions()->setWidth(600);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);

        // Rendu de la vue avec le graphique
        return $this->render('chart/index.html.twig', [
            'piechart' => $pieChart,
        ]);
    }
}