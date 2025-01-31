<?php

namespace App\Entity;

use App\Repository\LiaisonCoursCryptomonnaieRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LiaisonCoursCryptomonnaieRepository::class)]
class LiaisonCoursCryptomonnaie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private $nouveauValeur;

    #[ORM\Column(type: 'integer')]
    private $idCryptomonnaie;

    #[ORM\Column(type: 'datetime')]
    private $dateUpdate;


    public function getId(): ?int
    {
        return $this->id;
    }
    public function getNouveauValeur(): ?int
    {
        return $this->nouveauValeur;
    }

    public function setNouveauValeur(int $valeur): self
    {
        $this->nouveauValeur = $valeur;

        return $this;
    }
    public function getIdCryptomonnaie(): ?int
    {
        return $this->idCryptomonnaie;
    }

    public function setIdCryptomonnaie(int $cryptomonnaie): self
    {
        $this->idCryptomonnaie = $cryptomonnaie;

        return $this;
    }
    
    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->dateUpdate;
    }

    public function setDateUpdate(\DateTimeInterface $dateUpdate): self
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }
    
}

    
    
    
    
   
    

