<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method string getUserIdentifier()
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Serializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("users")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("users")]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups("users")]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email(message: "Votre email n'est pas valide.")]
    #[Groups("users")]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: '/^(?=.*[A-Z])(?=.*[0-9]).{8,}$/',
        message: "Le mot de passe doit comporter au moins 8 caractères, dont au moins une lettre majuscule et un chiffre."
    )]
    private ?string $password = null;

    #[Assert\EqualTo(
        propertyPath: "password",
        message: "Le mot de passe et la confirmation du mot de passe ne correspondent pas",
    )]
    private ?string $confirmPassword = null;

    #[ORM\Column(length: 255)]
    private ?string $roles = null;

    #[ORM\Column(type: "boolean")]
    private ?bool $isVerified = false;

    #[ORM\Column(nullable: true)]
    private ?string $verificationCode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    public function getRoles(): array
    {
        // Si $this->roles est null, retourner un tableau vide
        return $this->roles ? explode(',', $this->roles) : [];
    }
    
    public function setRoles(array $roles): self
    {
        // Convertir le tableau de rôles en une chaîne séparée par des virgules
        $this->roles = implode(',', $roles);
        return $this;
    }
    

    


    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getVerificationCode(): ?string
    {
        return $this->verificationCode;
    }

    public function setVerificationCode(?string $verificationCode): self
    {
        $this->verificationCode = $verificationCode;

        return $this;
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->nom,
            $this->prenom,
            $this->email,
            $this->password,
            $this->roles,
            $this->isVerified,
            $this->verificationCode,
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->id,
            $this->nom,
            $this->prenom,
            $this->email,
            $this->password,
            $this->roles,
            $this->isVerified,
            $this->verificationCode,
        ] = unserialize($serialized);
    }
    public function getRole(): array
    {
        return ['LIVREUR','GERANT','CLIENT'];
    }
}
