<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $pwd = null;

    #[ORM\Column(length: 255)]
    private ?string $role = null;

    #[ORM\OneToMany(mappedBy: 'idUser', targetEntity: Recette::class)]
    private Collection $recettes;

    #[ORM\OneToMany(mappedBy: 'user_id', targetEntity: Evennemnt::class)]
    private Collection $evennemnts;


    public function __construct()
    {
        $this->recettes = new ArrayCollection();
        $this->evennemnts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPwd(): ?string
    {
        return $this->pwd;
    }

    public function setPwd(string $pwd): static
    {
        $this->pwd = $pwd;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, Recette>
     */
    public function getRecettes(): Collection
    {
        return $this->recettes;
    }

    public function addRecette(Recette $recette): static
    {
        if (!$this->recettes->contains($recette)) {
            $this->recettes->add($recette);
            $recette->setIdUser($this);
        }

        return $this;
    }

    public function removeRecette(Recette $recette): static
    {
        if ($this->recettes->removeElement($recette)) {
            // set the owning side to null (unless already changed)
            if ($recette->getIdUser() === $this) {
                $recette->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Evennemnt>
     */
    public function getEvennemnts(): Collection
    {
        return $this->evennemnts;
    }

    public function addEvennemnt(Evennemnt $evennemnt): static
    {
        if (!$this->evennemnts->contains($evennemnt)) {
            $this->evennemnts->add($evennemnt);
            $evennemnt->setUserId($this);
        }

        return $this;
    }

    public function removeEvennemnt(Evennemnt $evennemnt): static
    {
        if ($this->evennemnts->removeElement($evennemnt)) {
            // set the owning side to null (unless already changed)
            if ($evennemnt->getUserId() === $this) {
                $evennemnt->setUserId(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom; // Assuming 'nomEvent' is a property of your entity that you want to use for string representation
    }
}
