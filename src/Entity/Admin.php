<?php
namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $userName = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $motDepasse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string // ✅ Correction du type de retour
    {
        return $this->userName;
    }

    public function getMotDepasse(): ?string // ✅ Correction du type de retour
    {
        return $this->motDepasse;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;
        return $this;
    }

    public function setMotDepasse(string $motDepasse): self
    {
        $this->motDepasse = $motDepasse;
        return $this;
    }
}
