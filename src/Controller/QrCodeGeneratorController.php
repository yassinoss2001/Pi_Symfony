<?php
 
namespace App\Controller;


use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\Alignment\LabelAlignmentLeft;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Font\NotoSans;
 


use Endroid\QrCode\ErrorCorrectionLevel;



use Endroid\QrCode\RoundBlockSizeMode;

use Endroid\QrCode\Writer\ValidationException;
class QrCodeGeneratorController extends AbstractController
{
    #[Route('/qr-codes', name: 'app_qr_codes')]
    public function index(): Response
    {
        $writer = new PngWriter();

        // Create QR code
        $qrCode = QrCode::create('Life is too short to be generating QR codes')
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
        $label = Label::create('Label')
            ->setTextColor(new Color(255, 0, 0));
        
        $result = $writer->write($qrCode, $logo, $label);
        $uploadsDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/';
        $qrCodeFilePath = $uploadsDirectory . 'qrcode.png';
        $result->saveToFile($qrCodeFilePath);
        
       
        return $this->render('menu/Qrcode/index.html.twig', [
            'result' => $result,
        ]);
        
    }
}