<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ConfigDeviseRepository;
#[ORM\Entity(repositoryClass: ConfigDeviseRepository::class)]
class ConfigDevise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Devise::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Devise $devise = null;

    #[ORM\ManyToOne(targetEntity: Devise::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Devise $deviseBase = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 8)]
    private ?string $valeur = null;

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
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

    public function getDeviseBase(): ?Devise
    {
        return $this->deviseBase;
    }

    public function setDeviseBase(?Devise $deviseBase): self
    {
        $this->deviseBase = $deviseBase;
        return $this;
    }

    public function getValeur(): ?string
    {
        return $this->valeur;
    }

    public function setValeur(string $valeur): self
    {
        $this->valeur = $valeur;
        return $this;
    }
}
