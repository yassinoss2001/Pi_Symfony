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
use TCPDF;

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
        $etapes = $form->get('etape')->getData(); // Récupérer les étapes du champ initial
        $etapesArray = explode("\n", $etapes); // Convertir les étapes en un tableau
        // Récupérer les étapes dynamiquement ajoutées
        $etapesDynamiques = $request->request->get('etape');
        foreach ($etapesDynamiques as $etape) {
            if (!empty($etape)) {
                $etapesArray[] = $etape;
            }
        }
        // Concaténer toutes les étapes en une seule chaîne
        $etapesConcat = implode("\n", $etapesArray);
        // Enregistrer les étapes concaténées dans l'entité Recette
        $recette->setEtape($etapesConcat);


// Récupérer les ingrédients du champ initial
$ingredients = $form->get('ingredients')->getData();
$ingredientsArray = explode("\n", $ingredients); // Convertir en un tableau

// Récupérer les ingrédients dynamiquement ajoutés
$ingredientsDynamiques = $request->request->get('ingredients');
foreach ($ingredientsDynamiques as $ingredient) {
    if (!empty($ingredient)) {
        $ingredientsArray[] = $ingredient;
    }
}
// Concaténer tous les ingrédients en une seule chaîne
$ingredientsConcat = implode("\n", $ingredientsArray);

// Enregistrer les ingrédients concaténés dans l'entité Recette
$recette->setIngredients($ingredientsConcat);



            // Gestion de l'image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $imageFileName
                );
                $recette->setImage($imageFileName);
            }

            // Gestion de la vidéo
            $videoFile = $form->get('video')->getData();
            if ($videoFile) {
                $videoFileName = uniqid().'.'.$videoFile->guessExtension();
                $videoFile->move(
                    $this->getParameter('videos_directory'),
                    $videoFileName
                );
                $recette->setVideo($videoFileName);
            }

            // Enregistrer l'entité Recette dans la base de données
            $entityManager = $this->getDoctrine()->getManager();
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
    #[Route('/{id}/pdf', name: 'app_recette_pdf', methods: ['GET'])]
public function generatePdf(Recette $recette): Response
{
    // Créer une nouvelle instance de TCPDF
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    // Définir les informations du document
    $pdf->SetCreator('Your App Name');
    $pdf->SetAuthor('Your Name');
    $pdf->SetTitle($recette->getTitre());

    // Ajouter une page
    $pdf->AddPage();

    // Couleur des titres
    $titreColor = array(241, 65, 65); // Couleur #f14141
    $pdf->SetTextColor($titreColor[0], $titreColor[1], $titreColor[2]);

    // Ajouter le contenu du PDF (titre, description, ingrédients, étapes)
    $pdf->SetFont('helvetica', 'B', 16);
    
    // Titre de la recette
    $pdf->Cell(0, 10, $recette->getTitre(), 0, 1, 'C');

    // Réinitialiser la couleur à noir pour le contenu suivant
    $pdf->SetTextColor(0, 0, 0); // Noir

    // Titre de la description
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Description:', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->MultiCell(0, 10, $recette->getDescription(), 0, 'L');

    // Titre des ingrédients
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Ingrédients:', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->MultiCell(0, 10, $recette->getIngredients(), 0, 'L');

    // Titre des étapes
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Étapes:', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);

    // Séparer les étapes et ajouter des numéros
    $etapes = explode("\n", $recette->getEtape());
    foreach ($etapes as $key => $etape) {
        // Ajouter le numéro de l'étape et son contenu
        $pdf->MultiCell(0, 10, 'Étape ' . ($key + 1) . ': ' . $etape, 0, 'L');
    }
    // Ajouter l'image de Yammi Food à droite du PDF
    $logoPath = $this->getParameter('kernel.project_dir') . '/public/assets/images/yammifood.png';
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, 150, 20, 40);
    }
    // Ajouter l'image au PDF
    $imagePath = $this->getParameter('images_directory') . '/' . $recette->getImage();
    if (file_exists($imagePath)) {
        $pdf->Image($imagePath, 10, 130, 100);
    }

    // Nommer et envoyer le fichier PDF au navigateur
    $pdfName = 'recette_' . $recette->getId() . '.pdf';
    $pdf->Output($pdfName, 'D');

    // Retourner une réponse vide (le téléchargement du fichier se fait via le navigateur)
    return new Response();
}

}
    

