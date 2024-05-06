<?php

namespace App\Entity;

use App\Repository\FavorisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: FavorisRepository::class)]
#[Broadcast]
class Favoris
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Evennemnt::class)]
    private ?Evennemnt $evennemnt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvennemnt(): ?Evennemnt
    {
        return $this->evennemnt;
    }

    public function setEvennemnt(?Evennemnt $evennemnt): self
    {
        $this->evennemnt = $evennemnt;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
}

