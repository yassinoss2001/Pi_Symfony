<?php

namespace App\Entity;

use App\Repository\RecetteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RecetteRepository::class)]
class Recette
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le titre ne peut pas être vide")]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide")]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Les ingrédients ne peuvent pas être vides")]
    private ?string $ingredients = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Les étapes ne peuvent pas être vides")]
    private ?string $etape = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'image ne peut pas être vide")]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La vidéo ne peut pas être vide")]
    #[Assert\File(
        mimeTypes: ["video/mp4"],
        mimeTypesMessage: "Veuillez télécharger un fichier vidéo au format MP4"
    )]
    private ?string $video = null;

    #[ORM\ManyToOne(inversedBy: 'recettes')]
    private ?User $idUser = null;

    #[ORM\OneToMany(mappedBy: 'id_recette', targetEntity: Avis::class)]
    private Collection $avis;

    public function __construct()
    {
        $this->avis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

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

    public function getIngredients(): ?string
    {
        return $this->ingredients;
    }

    public function setIngredients(string $ingredients): static
    {
        $this->ingredients = $ingredients;

        return $this;
    }

    public function getEtape(): ?string
    {
        return $this->etape;
    }

    public function setEtape(string $etape): static
    {
        $this->etape = $etape;

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

    public function getVideo(): ?string
    {
        return $this->video;
    }

    public function setVideo(string $video): static
    {
        $this->video = $video;

        return $this;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): static
    {
        $this->idUser = $idUser;

        return $this;
    }

    
public function addIngredient(string $ingredient): static
{
    if ($this->ingredients === null) {
        $this->ingredients = $ingredient;
    } else {
        $this->ingredients .= ',' . $ingredient;
    }

    return $this;
}

/**
 * @return Collection<int, Avis>
 */
public function getAvis(): Collection
{
    return $this->avis;
}

public function addAvi(Avis $avi): static
{
    if (!$this->avis->contains($avi)) {
        $this->avis->add($avi);
        $avi->setIdRecette($this);
    }

    return $this;
}

public function removeAvi(Avis $avi): static
{
    if ($this->avis->removeElement($avi)) {
        // set the owning side to null (unless already changed)
        if ($avi->getIdRecette() === $this) {
            $avi->setIdRecette(null);
        }
    }

    return $this;
}
}
