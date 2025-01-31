<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserTokenRepository")
 */
#[ORM\Entity(repositoryClass: 'App\Repository\UserTokenRepository')]
class UserToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $idUser = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $token = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $tempsExpiration = null;

    // Getters et Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): self
    {
        $this->idUser = $idUser;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function getTempsExpiration(): ?\DateTimeInterface
    {
        return $this->tempsExpiration;
    }

    public function setTempsExpiration(\DateTimeInterface $tempsExpiration): self
    {
        $this->tempsExpiration = $tempsExpiration;
        return $this;
    }
}
