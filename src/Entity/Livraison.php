<?php

namespace App\Entity;

use App\Repository\LivraisonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LivraisonRepository::class)]
class Livraison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $idCommande = null;

    #[ORM\Column]
    private ?int $idLivreur = null;

    #[ORM\Column]
    private ?int $heureDepart = null;

    #[ORM\Column(length: 255)]
    private ?string $etatLivraison = null;

    #[ORM\Column(length: 500)]
    private ?string $commentairesLivreur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCommande(): ?Commande
    {
        return $this->idCommande;
    }

    public function setIdCommande(Commande $idCommande): static
    {
        $this->idCommande = $idCommande;

        return $this;
    }

    public function getIdLivreur(): ?int
    {
        return $this->idLivreur;
    }

    public function setIdLivreur(int $idLivreur): static
    {
        $this->idLivreur = $idLivreur;

        return $this;
    }

    public function getHeureDepart(): ?int
    {
        return $this->heureDepart;
    }

    public function setHeureDepart(int $heureDepart): static
    {
        $this->heureDepart = $heureDepart;

        return $this;
    }

    public function getEtatLivraison(): ?string
    {
        return $this->etatLivraison;
    }

    public function setEtatLivraison(string $etatLivraison): static
    {
        $this->etatLivraison = $etatLivraison;

        return $this;
    }

    public function getCommentairesLivreur(): ?string
    {
        return $this->commentairesLivreur;
    }

    public function setCommentairesLivreur(string $commentairesLivreur): static
    {
        $this->commentairesLivreur = $commentairesLivreur;

        return $this;
    }
}
