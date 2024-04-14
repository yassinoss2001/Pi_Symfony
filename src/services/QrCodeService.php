<?php
namespace App\services;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh as QrCodeErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use App\Entity\Restaurant;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
class QrCodeService
{ /**
    * @var BuilderInterface
    */
   protected $builder;

   public function __construct(BuilderInterface $builder)
   {
       $this->builder = $builder;
   }

   public function qrcode(array $restaurantData)
    {
        // Extract restaurant data
        $nom = $restaurantData['nom'];
        $speciality = $restaurantData['speciality'];
        $telephone = $restaurantData['telephone'];
        $place = $restaurantData['place'];
        // Construct QR code data
        $qrData = "Nom: $nom\nSpecialité: $speciality\nTéléphone: $telephone\nLieu: $place";

        // Set QR code properties
        $result = $this->builder
            ->data($qrData)
            ->encoding(new Encoding('UTF-8'))
            ->size(400)
            ->margin(10)
            ->labelText($nom) 
            ->labelMargin(new Margin(15, 5, 5, 5))
            ->backgroundColor(new Color(221, 158, 3))
            ->build();

        // Generate name
        $namePng = uniqid('', '') . '.png';
        // Save QR code image to file
        $result->saveToFile('public\img\qr-code' . $namePng);

        // Return data URI of the QR code
        return $result->getDataUri();
    }

}