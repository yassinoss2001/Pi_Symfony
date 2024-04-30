<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?User $user_id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Evennemnt $evennement_id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_reservation = null;

    #[ORM\Column]
    private ?int $nombre_participants = null;

    #[ORM\Column(length: 100)]
    private ?string $email_contact = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getEvennementId(): ?Evennemnt
    {
        return $this->evennement_id;
    }

    public function setEvennementId(?Evennemnt $evennement_id): static
    {
        $this->evennement_id = $evennement_id;

        return $this;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->date_reservation;
    }

    public function setDateReservation(?\DateTimeInterface $date_reservation): static
    {
        $this->date_reservation = $date_reservation;
    
        return $this;
    }
    

    public function getNombreParticipants(): ?int
    {
        return $this->nombre_participants;
    }

    public function setNombreParticipants(int $nombre_participants): static
    {
        $this->nombre_participants = $nombre_participants;

        return $this;
    }

    public function getEmailContact(): ?string
    {
        return $this->email_contact;
    }

    public function setEmailContact(string $email_contact): static
    {
        $this->email_contact = $email_contact;

        return $this;
    }
    public function __toString(): string
    {
        return sprintf(
            'Reservation #%d: Date: %s, Participants: %d, Contact Email: %s',
            $this->id,
            $this->date_reservation->format('Y-m-d'),
            $this->nombre_participants,
            $this->email_contact
        );
    }
}
