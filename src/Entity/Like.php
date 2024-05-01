<?php

namespace App\Entity;

use App\Repository\LikeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $id_user = null;

    #[ORM\Column(nullable: true)]
    private ?int $id_item = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?user
    {
        return $this->id_user;
    }

    public function setIdUser(?user $id_user): static
    {
        $this->id_user = $id_user;

        return $this;
    }

    public function getIdItem(): ?int
    {
        return $this->id_item;
    }

    public function setIdItem(?int $id_item): static
    {
        $this->id_item = $id_item;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;

        return $this;
    }
}
