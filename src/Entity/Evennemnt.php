<?php

namespace App\Entity;

use App\Repository\EvennemntRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EvennemntRepository::class)]
class Evennemnt
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
 #[Assert\NotBlank(message: "Le nom de l'événement ne peut pas être vide.")]
    #[Assert\Length(max: 100, maxMessage: "Le nom de l'événement ne peut pas dépasser {{ limit }} caractères.")]
    #[ORM\Column(length: 100)]
    private ?string $nom_event = null;

    #[Assert\NotBlank(message: "La description de l'événement ne peut pas être vide.")]
    #[ORM\Column(type: Types::TEXT)]
    private ?string $desc_event = null;

    #[Assert\NotBlank(message: "La date de début ne peut pas être vide.")]
    #[Assert\Date(message: "Date de début invalide.")]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[Assert\NotBlank(message: "La date de fin ne peut pas être vide.")]
    #[Assert\Date(message: "Date de fin invalide.")]
    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_fin = null;

    #[Assert\NotBlank(message: "Le lieu de l'événement ne peut pas être vide.")]
    #[Assert\Length(max: 255, maxMessage: "Le lieu de l'événement ne peut pas dépasser {{ limit }} caractères.")]
    #[ORM\Column(length: 255)]
    private ?string $lieu_evenement = null;

    #[Assert\NotBlank(message: "Le nombre de participants ne peut pas être vide.")]
    #[Assert\Type(type: "integer", message: "Le nombre de participants doit être un nombre entier.")]
    #[ORM\Column]
    private ?int $Nbr_participants = null;

    #[Assert\NotBlank(message: "L'heure de début ne peut pas être vide.")]
    #[Assert\Time(message: "Heure de début invalide.")]
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $Time_debut = null;

    #[Assert\NotBlank(message: "L'heure de fin ne peut pas être vide.")]
    #[Assert\Time(message: "Heure de fin invalide.")]
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $Time_fin = null;

    #[Assert\NotBlank(message: "Le nom du restaurant ne peut pas être vide.")]
    #[Assert\Length(max: 100, maxMessage: "Le nom du restaurant ne peut pas dépasser {{ limit }} caractères.")]
    #[ORM\Column(length: 100)]
    private ?string $NameResto = null;

    #[Assert\NotBlank(message: "Le chemin de l'image ne peut pas être vide.")]
    #[Assert\Length(max: 255, maxMessage: "Le chemin de l'image ne peut pas dépasser {{ limit }} caractères.")]
    #[ORM\Column(length: 255)]
    private ?string $image_path = null;

    #[ORM\ManyToOne(inversedBy: 'evennemnts')]
    private ?User $user_id = null;

    #[ORM\OneToMany(mappedBy: 'evennement_id', targetEntity: Reservation::class)]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEvent(): ?string
    {
        return $this->nom_event;
    }

    public function setNomEvent(string $nom_event): static
    {
        $this->nom_event = $nom_event;

        return $this;
    }

    public function getDescEvent(): ?string
    {
        return $this->desc_event;
    }

    public function setDescEvent(string $desc_event): static
    {
        $this->desc_event = $desc_event;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getLieuEvenement(): ?string
    {
        return $this->lieu_evenement;
    }

    public function setLieuEvenement(string $lieu_evenement): static
    {
        $this->lieu_evenement = $lieu_evenement;

        return $this;
    }

    public function getNbrParticipants(): ?int
    {
        return $this->Nbr_participants;
    }

    public function setNbrParticipants(int $Nbr_participants): static
    {
        $this->Nbr_participants = $Nbr_participants;

        return $this;
    }

    public function getTimeDebut(): ?\DateTimeInterface
    {
        return $this->Time_debut;
    }

    public function setTimeDebut(\DateTimeInterface $Time_debut): static
    {
        $this->Time_debut = $Time_debut;

        return $this;
    }

    public function getTimeFin(): ?\DateTimeInterface
    {
        return $this->Time_fin;
    }

    public function setTimeFin(\DateTimeInterface $Time_fin): static
    {
        $this->Time_fin = $Time_fin;

        return $this;
    }

    public function getNameResto(): ?string
    {
        return $this->NameResto;
    }

    public function setNameResto(string $NameResto): static
    {
        $this->NameResto = $NameResto;

        return $this;
    }

    public function getImage_Path(): ?string
    {
        return $this->image_path;
    }

    public function setImage_Path(string $image_path): static
    {
        $this->image_path = $image_path;

        return $this;
    }

    public function getUserId(): ?user
    {
        return $this->user_id;
    }

    public function setUserId(?user $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setEvennementId($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getEvennementId() === $this) {
                $reservation->setEvennementId(null);
            }
        }

        return $this;
    
    }

    public function __toString(): string
    {
        return $this->nom_event; // Assuming 'nomEvent' is a property of your entity that you want to use for string representation
    }
}
