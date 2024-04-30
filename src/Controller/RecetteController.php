<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;
use App\Entity\Avis;
use App\Form\AvisType;
use Endroid\QrCode\QrCode;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Endroid\QrCode\Writer\PngWriter;




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

    #[Route('/liste', name: 'app_recette_liste', methods: ['GET'])]
    public function liste(RecetteRepository $recetteRepository): Response
    {
        $recettes = $recetteRepository->findAll(); // Récupérer toutes les recettes
    
        return $this->render('recette/liste_recette.html.twig', [
            'recettes' => $recettes,
        ]);
    }
    #[Route('/{id}', name: 'app_recette_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Recette $recette, AvisRepository $avisRepository): Response
    {
        // Récupérer les avis associés à cette recette
        $avisRecette = $avisRepository->findBy(['id_recette' => $recette]);

        // Calculer le total des avis
        $totalAvis = count($avisRecette);

        $totalNotes = 0;
    foreach ($avisRecette as $avis) {
        $totalNotes += $avis->getNote();
    }
        // Créer un nouveau formulaire d'avis
        $avis = new Avis();
        $avis->setDate(new \DateTime());
        $avis->setIdRecette($recette);
    
        // Vérifier si la requête contient la valeur de la note
        $noteValue = $request->request->get('note');
    
        // Vérifier si la valeur de la note est valide
        if ($noteValue !== null && is_numeric($noteValue)) {
            $note = intval($noteValue); // Convertir en entier
            $avis->setNote($note);
        } else {
            // Gérer le cas où la note n'est pas valide
            // Vous pouvez définir une valeur par défaut ou afficher un message d'erreur
        }
    
        $avisForm = $this->createForm(AvisType::class, $avis);
        $avisForm->handleRequest($request);
    
        if ($avisForm->isSubmitted() && $avisForm->isValid()) {
            // Associer l'avis à la recette et enregistrer en base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($avis);
            $entityManager->flush();
    
            // Rediriger vers la même page après la soumission du formulaire
            return $this->redirectToRoute('app_recette_show', ['id' => $recette->getId()]);
        }
    
        return $this->render('recette/show.html.twig', [
            'recette' => $recette,
            'avisForm' => $avisForm->createView(),
            'avisRecette' => $avisRecette, // Passer les avis à la vue
            'totalAvis' => $totalAvis, // Passer le total des avis à la vue
            'totalNotes' => $totalNotes,
        ]);
    }

    #[Route('/qr-code/{id}', name: 'qr_code')]
public function generateQrCode($id)
{
    $recette = $this->getDoctrine()->getRepository(Recette::class)->find($id);
    
    // Generate the QR code content
    $content = sprintf(
        'Titre recette: %s, Description: %s, Ingrédients: %s, Étapes: %s',
        $recette->getTitre(),
        $recette->getDescription(),
        $this->formatList($recette->getIngredients()),
        $this->formatSteps($recette->getEtape())
    );

    // Create a QR code
    $qrCode = new QrCode($content);

    // Create a writer
    $writer = new PngWriter();

    // Generate a result
    $result = $writer->write($qrCode);

    // Create a response
    $response = new QrCodeResponse($result);

    return $response;
    
}

#[Route('/{id}/edit', name: 'app_recette_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Recette $recette, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(RecetteType::class, $recette);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Vérifier si les champs d'image et de vidéo sont vides
        $imageFile = $form->get('image')->getData();
        $videoFile = $form->get('video')->getData();

        if (!$imageFile && !$videoFile) {
            // Les champs d'image et de vidéo sont vides, ne pas modifier les données
            $entityManager->persist($recette); // Non nécessaire, mais pour la clarté
            $entityManager->flush();
        } else {
            // Au moins un des champs est rempli, traiter les données d'image et de vidéo
            if ($imageFile) {
                // Gestion de l'image comme dans votre action new
                $imageFileName = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $imageFileName
                );
                $recette->setImage($imageFileName);
            }
            if ($videoFile) {
                // Gestion de la vidéo comme dans votre action new
                $videoFileName = uniqid().'.'.$videoFile->guessExtension();
                $videoFile->move(
                    $this->getParameter('videos_directory'),
                    $videoFileName
                );
                $recette->setVideo($videoFileName);
            }

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_recette_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('recette/edit.html.twig', [
        'recette' => $recette,
        'form' => $form->createView(),
    ]);
}
#[Route('/{id}/delete', name: 'app_recette_delete', methods: ['POST'])]
public function delete(Request $request, Recette $recette, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete' . $recette->getId(), $request->request->get('_token'))) {
        $entityManager->remove($recette);
        $entityManager->flush();
        $this->addFlash('success', 'Recette supprimée avec succès.');
    } else {
        $this->addFlash('error', 'Erreur lors de la suppression de la recette.');
    }

    return $this->redirectToRoute('app_recette_liste');
}

#[Route('/avis/{id}/delete', name: 'app_avis_delete', methods: ['POST'])]
public function deleteAvis(Request $request, Avis $avis, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete' . $avis->getId(), $request->request->get('_token'))) {
        $entityManager->remove($avis);
        $entityManager->flush();

        $this->addFlash('success', 'L\'avis a été supprimé avec succès.');
    } else {
        $this->addFlash('error', 'Erreur lors de la suppression de l\'avis.');
    }

    // Rediriger vers la page de la recette ou une autre page appropriée
    return $this->redirectToRoute('app_recette_show', ['id' => $avis->getIdRecette()->getId()]);
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

private function formatList($list)
{
    // Séparer la liste d'ingrédients par les sauts de ligne
    $ingredientsArray = explode("\n", $list);
    
    // Initialiser une chaîne vide pour stocker les ingrédients formatés
    $formattedIngredients = '';

    // Parcourir chaque ingrédient et les ajouter à la chaîne formatée avec une numérotation
    foreach ($ingredientsArray as $index => $ingredient) {
        // Ignorer les lignes vides
        if (!empty(trim($ingredient))) {
            $formattedIngredients .= ($index + 1) . ". " . trim($ingredient) . "\n";
        }
    }

    // Retourner la liste d'ingrédients formatée
    return $formattedIngredients;
}

private function formatSteps($steps)
{
    // Séparer les étapes par les sauts de ligne
    $stepsArray = explode("\n", $steps);
    
    // Initialiser une chaîne vide pour stocker les étapes formatées
    $formattedSteps = '';

    // Parcourir chaque étape et les ajouter à la chaîne formatée avec une numérotation
    foreach ($stepsArray as $index => $step) {
        // Ignorer les lignes vides
        if (!empty(trim($step))) {
            $formattedSteps .= "Étape " . ($index + 1) . " : " . trim($step) . "\n";
        }
    }

    // Retourner les étapes formatées
    return $formattedSteps;
}

}
    

