<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $idClient = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Entrer un Menu valide')]
    private ?int $idMenu = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Entrer une Adresse valide')]
    #[Assert\Length(min: 6, max: 255, minMessage: 'Adresse doit etre min {{ limit }} characters', maxMessage: 'Adresse doit etre max {{ limit }} characters')]
    private ?string $adresse = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Entrer une Longitude valide')]
    private ?float $longitude = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Entrer une Laitude valide')]
    private ?float $latitude = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column(length: 255)]
    private ?string $etatCommande = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Entrer une Mode Paiement valide')]
    private ?string $modePayement = null;

    #[ORM\Column(length: 500)]
    #[Assert\NotBlank(message: 'Entrer une Remarque valide')]
    private ?string $remarque = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdClient(): ?int
    {
        return $this->idClient;
    }

    public function setIdClient(int $idClient): static
    {
        $this->idClient = $idClient;

        return $this;
    }

    public function getIdMenu(): ?int
    {
        return $this->idMenu;
    }

    public function setIdMenu(int $idMenu): static
    {
        $this->idMenu = $idMenu;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): static
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    public function getEtatCommande(): ?string
    {
        return $this->etatCommande;
    }

    public function setEtatCommande(string $etatCommande): static
    {
        $this->etatCommande = $etatCommande;

        return $this;
    }

    public function getModePayement(): ?string
    {
        return $this->modePayement;
    }

    public function setModePayement(string $modePayement): static
    {
        $this->modePayement = $modePayement;

        return $this;
    }

    public function getRemarque(): ?string
    {
        return $this->remarque;
    }

    public function setRemarque(string $remarque): static
    {
        $this->remarque = $remarque;

        return $this;
    }
}
