<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DeviseRepository;
#[ORM\Entity(repositoryClass: DeviseRepository::class)]
class Devise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 8)]
    private ?string $valeur = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $nom = null;

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
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
    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }
    public function transformationEuro(float $coursDevise, float $montant):float
    {
        return $montant/$coursDevise;
    }
    public function transformationAutre(float $coursDevise, float $montant):float
    {
        return $montant * $coursDevise;
    }
}
?>