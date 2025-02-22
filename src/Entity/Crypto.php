<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CryptoRepository;
#[ORM\Entity(repositoryClass: CryptoRepository::class)]
class Crypto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', length: 5)]
    private ?string $symbol = null;

    // Getters and Setters

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

    public function getSymbol() {
        return $this->symbol;
    }
    
    public function setSymbol(string $symbol) {
        $this->symbol = $symbol;
        return $this;
    }
}
?>