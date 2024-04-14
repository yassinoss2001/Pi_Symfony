<?php

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
class Restaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $idCategorie = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'veuillez saisir votre nom')]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'veuillez saisir votre SPECIALITE')]
    private ?string $speciality = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'veuillez saisir votre telephone')]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'veuillez saisir votre description')]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'veuillez saisir votre place')]
    private ?string $place = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'veuillez saisir votre rate')]
    private ?string $rate = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'veuillez ajouter votre image')]
    private ?string $image = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCategorie(): ?string
    {
        return $this->idCategorie;
    }

    public function setIdCategorie(string $idCategorie): static
    {
        $this->idCategorie = $idCategorie;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getSpeciality(): ?string
    {
        return $this->speciality;
    }

    public function setSpeciality(string $speciality): static
    {
        $this->speciality = $speciality;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(string $place): static
    {
        $this->place = $place;

        return $this;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(string $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
