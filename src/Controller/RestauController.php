<?php

namespace App\Controller;
use App\services\QrCodeService;
use App\Entity\Restaurant;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\Builder\BuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface; // Import manquant
Use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


#[Route('/restau')]
class RestauController extends AbstractController
{
   #[Route('/', name: 'app_restau_index', methods: ['GET'])]
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        return $this->render('restau/index.html.twig', [
            'restaurants' => $restaurantRepository->findAll(),
        ]);
    }

    #[Route('/search', name: 'app_restau_search', methods: ['GET', 'POST'])]
    public function listRestaurantWithSearch(Request $request, RestaurantRepository $restaurantRepository): Response
    {
        

        $searchForm = $this->createFormBuilder()
            ->add('query', TextType::class, ['label' => 'Recherche'])
            ->add('search', SubmitType::class, ['label' => 'Rechercher'])
            ->getForm();

        $searchForm->handleRequest($request);
        $restaurants = [];

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $data = $searchForm->getData();
            $query = $data['query'];
           
            $restaurants = $restaurantRepository->searchByQuery($query);
        }

        return $this->render('restau/search.html.twig', [
            'restaurants' => $restaurants,
            'searchForm' => $searchForm->createView(),
            
        ]);
    }


    #[Route('/new', name: 'app_restau_new', methods: ['GET', 'POST'])]
    public function new(Request $request,RestaurantRepository $restaurantRepository, EntityManagerInterface $entityManager): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $imageFileName
                );
                $restaurant->setImage($imageFileName);
            }
            $restaurantRepository->save($restaurant, true);
            $restaurantRepository->sms();
           

            return $this->redirectToRoute('app_restau_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('restau/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }



    #[Route('/sort', name: 'sort')]
    public function sort(Request $request, RestaurantRepository $restaurantRepository): Response
    {
        $place = $request->query->get('place');
        $restaurants = [];
    
        if ($place) {
            $restaurants = $restaurantRepository->findByPlace($place);
        } else {
            // Si aucun lieu n'est spécifié, triez tous les restaurants
            $restaurants = $restaurantRepository->findAll();
        }
    
        return $this->render('restau/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }

    #[Route('/{id}', name: 'app_restau_show', methods: ['GET'])]
    public function show(Restaurant $restaurant): Response
    { 
        return $this->render('restau/show.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_restau_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {   
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->add('update',SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingImage = $restaurant->getImage();
            $newImage = $form->get('image')->getData();
    
            if (!$newImage) {
                // If no new image is provided, keep the existing one
                $restaurant->setImage($existingImage);
            } else {
                // If a new image is provided, handle it as in the 'new' action
                $imageFileName = uniqid().'.'.$newImage->guessExtension();
                $newImage->move(
                    $this->getParameter('images_directory'),
                    $imageFileName
                );
                $restaurant->setImage($imageFileName);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_restau_index', [], Response::HTTP_SEE_OTHER);
        }

       
    
        return $this->renderForm('restau/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_restau_delete', methods: ['POST'])]
    public function delete(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$restaurant->getId(), $request->request->get('_token'))) {
            $entityManager->remove($restaurant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_restau_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/listpdf', name: 'RESTAU_data_download')]
    public function list(RestaurantRepository $restaurantRepository, $id)
    {
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $pdfOptions->setIsRemoteEnabled(true);
    
        // On instancie Dompdf
        $dompdf = new Dompdf($pdfOptions);
        
        $restaurant = $this->getDoctrine()->getRepository(Restaurant::class)->find($id);
    
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('restau/pdf.html.twig', [
            'restaurant' => $restaurant,
        ]);
    
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
    
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');
    
        // Render the HTML as PDF
        $dompdf->render();
    
        $fichier = 'Liste-RESTAURANT' . '.pdf';
        
        // Output the generated PDF to Browser (force download)
        $dompdf->stream($fichier, [
            "Attachment" => true
        ]);
        
        return new Response();
    }



    #[Route('/scan-qr', name: 'app_restau_scan_qr', methods: ['GET'])]
    public function scanQr(Request $request, QrCodeService $qrCodeService): Response
    {
        // Récupérer les données du code QR scanné depuis la requête
        $data = $request->query->get('qr_data');
    
        // Utiliser ces données pour générer un nouveau code QR pour le restaurant
        $qrCode = $qrCodeService->qrcode($data);
    
        // Passer le code QR à la vue pour l'afficher
        return $this->render('restau/qr_code.html.twig', [
            'qrCode' => $qrCode
        ]);
    }

 
}
