<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserTransactionRepository")
 */
#[ORM\Entity(repositoryClass: 'App\Repository\UserTransactionRepository')]
class UserTransaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $idUser = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 8)]
    private ?string $entre = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 8)]
    private ?string $sortie = null;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Devise')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Devise $devise = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $datetime = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 8)]
    private ?string $etat = null;

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

    public function getEntre(): ?string
    {
        return $this->entre;
    }

    public function setEntre(string $entre): self
    {
        $this->entre = $entre;
        return $this;
    }

    public function getSortie(): ?string
    {
        return $this->sortie;
    }

    public function setSortie(string $sortie): self
    {
        $this->sortie = $sortie;
        return $this;
    }
    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(string $etat): self
    {
        $this->etat = $etat;
        return $this;
    }

    public function getDevise(): ?Devise
    {
        return $this->devise;
    }

    public function setDevise(?Devise $devise): self
    {
        $this->devise = $devise;
        return $this;
    }
    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;
        return $this;
    }
}
