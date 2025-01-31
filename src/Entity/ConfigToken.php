<?php
namespace App\Entity;
use App\Repository\ConfigTokenRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: ConfigTokenRepository::class)]
class ConfigToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $dureeDeVieToken = null;

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDureeDeVieToken(): ?int
    {
        return $this->dureeDeVieToken;
    }

    public function setDureeDeVieToken(int $dureeDeVieToken): self
    {
        $this->dureeDeVieToken = $dureeDeVieToken;
        return $this;
    }
}
?>