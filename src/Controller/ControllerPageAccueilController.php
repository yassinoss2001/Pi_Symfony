<?php
//this is the controller of the landing page  of the website
namespace App\Controller;
use App\Entity\Menu;
use App\Entity\Like;
use App\Entity\Supplement;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Alignment\LabelAlignmentLeft;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Symfony\Component\HttpFoundation\Request;

class ControllerPageAccueilController extends AbstractController
{
    #[Route('/accueil', name: 'app_controller_page_accueil')]
    public function index(): Response
    { 
        $menuRepository = $this->getDoctrine()->getRepository(Menu::class);
        $supplementRepository = $this->getDoctrine()->getRepository(Supplement::class);
        $likeRepository = $this->getDoctrine()->getRepository(Like::class);
    
        // Get all menu and supplement items
        $menuItems = $menuRepository->findAll();
        $supplementItems = $supplementRepository->findAll();
    
        // Initialize arrays to store like counts for each item
        $menuLikeCounts = [];
        $supplementLikeCounts = [];
    
        // Count likes for each menu item
        foreach ($menuItems as $menuItem) {
            $menuLikeCounts[$menuItem->getId()] = $likeRepository->countLikesByItemId($menuItem->getId(), 'menu');
        }
    
        // Count likes for each supplement item
        foreach ($supplementItems as $supplementItem) {
            $supplementLikeCounts[$supplementItem->getId()] = $likeRepository->countLikesByItemId($supplementItem->getId(), 'supplement');
        }
    
        return $this->render('controller_page_accueil/index.html.twig', [
            'menuItems' => $menuItems,
            'suppItems' => $supplementItems,
            'menuLikeCounts' => $menuLikeCounts,
            'supplementLikeCounts' => $supplementLikeCounts,
        ]);
    }

    #[Route('/fmenu/{id}', name: 'menu_details')]
    public function menuDetails(Menu $menu): Response
    {   $menuItems = $this->getDoctrine()->getRepository(Menu::class)->findAll();

        $writer = new PngWriter();
        $qrCodeContent = sprintf(
            "Nom: %s\nDescription: %s\nCalories: %s\nPrix: %s",
            $menu->getNom(),
            $menu->getDescription(),
            $menu->getPrix(),
            $menu->getCalories()
           
        );
        $entityManager = $this->getDoctrine()->getManager();
        $likeRepository = $entityManager->getRepository(Like::class);
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy([], ['id' => 'ASC']);
    
     
        $existingLike = $likeRepository->findOneBy([
            'id_user' => $user,
            'id_item' => $menu,
            'type' => 'menu',
        ]);
    
        // If a like exists, delete it; otherwise, create a new like
        $likeExists = $existingLike !== null;
        // Create QR code
        $qrCode = QrCode::create($qrCodeContent)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
        
        // Create generic logo
        $logo = Logo::create(__DIR__.'/QrCodes/logo.png')
            ->setResizeToWidth(50)
            ->setPunchoutBackground(true)
        ;
        
        // Create generic label
        $label = Label::create('YummyFood')
            ->setTextColor(new Color(255, 0, 0));
        
        $result = $writer->write($qrCode, $logo, $label);
        $uploadsDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/';
        $qrCodeFilePath = $uploadsDirectory . 'qrcode.png';
        $result->saveToFile($qrCodeFilePath);


        return $this->render('menu/details.html.twig', [
            'menu' => $menu,
            'menuItems' => $menuItems,
            'likeExists' => $likeExists,
        ]);
    }

    #[Route('/fmenu/{id}/like', name: 'app_menu_like', methods: ['GET', 'POST'])]
    public function mtoggleLike(Request $request, Menu $menu): Response
    { $menuItems = $this->getDoctrine()->getRepository(Menu::class)->findAll();
        $entityManager = $this->getDoctrine()->getManager();
        $likeRepository = $entityManager->getRepository(Like::class);
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy([], ['id' => 'ASC']);
    
        // Check if a like entity already exists for the user and menu item
        $existingLike = $likeRepository->findOneBy([
            'id_user' => $user,
            'id_item' => $menu->getId(),
            'type' => 'menu',
        ]);
    
        // If a like exists, delete it; otherwise, create a new like
        if ($existingLike) {
            $entityManager->remove($existingLike);
        } else {
            $like = new Like();
            $like->setIdUser($user);
            $like->setIdItem($menu->getId());
            $like->setType('menu');
            $entityManager->persist($like);
        }
    
        $entityManager->flush();
    
        // Redirect back to the menu page after toggling the like
        return $this->redirectToRoute('menu_details', ['id' => $menu->getId()]);
    }
    #[Route('/fsupplement/{id}/like', name: 'app_supplement_like', methods: ['GET', 'POST'])]
    public function stoggleLike(Request $request, Supplement $supplement): Response
    { $supplementItems = $this->getDoctrine()->getRepository(Supplement::class)->findAll();
        $entityManager = $this->getDoctrine()->getManager();
        $likeRepository = $entityManager->getRepository(Like::class);
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy([], ['id' => 'ASC']);
    
        // Check if a like entity already exists for the user and menu item
        $existingLike = $likeRepository->findOneBy([
            'id_user' => $user,
            'id_item' => $supplement->getId(),
            'type' => 'supplement',
        ]);
    
        // If a like exists, delete it; otherwise, create a new like
        if ($existingLike) {
            $entityManager->remove($existingLike);
        } else {
            $like = new Like();
            $like->setIdUser($user);
            $like->setIdItem($supplement->getId());
            $like->setType('supplement');
            $entityManager->persist($like);
        }
    
        $entityManager->flush();
    
        // Redirect back to the menu page after toggling the like
        return $this->redirectToRoute('supplement_details', ['id' => $supplement->getId()]);
    }

    #[Route('/fsupplement/{id}', name: 'supplement_details')]
public function supplementDetails(Supplement $supplement): Response
{
    
    $supplementItems = $this->getDoctrine()->getRepository(Supplement::class)->findAll();

        $writer = new PngWriter();
        $qrCodeContent = sprintf(
            "Nom: %s\nPrix: %s",
            $supplement->getNom(),
        
            $supplement->getPrix(),
    
           
        );
        $entityManager = $this->getDoctrine()->getManager();
        $likeRepository = $entityManager->getRepository(Like::class);
        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy([], ['id' => 'ASC']);
    
     
        $existingLike = $likeRepository->findOneBy([
            'id_user' => $user,
            'id_item' => $supplement,
            'type' => 'supplement',
        ]);
    
        // If a like exists, delete it; otherwise, create a new like
        $likeExists = $existingLike !== null;
        // Create QR code
        $qrCode = QrCode::create($qrCodeContent)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
        
        // Create generic logo
        $logo = Logo::create(__DIR__.'/QrCodes/logo.png')
            ->setResizeToWidth(50)
            ->setPunchoutBackground(true)
        ;
        
        // Create generic label
        $label = Label::create('YummyFood')
            ->setTextColor(new Color(255, 0, 0));
        
        $result = $writer->write($qrCode, $logo, $label);
        $uploadsDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/';
        $qrCodeFilePath = $uploadsDirectory . 'qrcode.png';
        $result->saveToFile($qrCodeFilePath);


        return $this->render('supplement/details.html.twig', [
            'supplement' => $supplement,
            'supplementItems' => $supplementItems,
            'likeExists' => $likeExists,
        ]);
    }


    

}
