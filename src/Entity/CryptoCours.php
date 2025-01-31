<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CryptoCoursRepository;
#[ORM\Entity(repositoryClass: CryptoCoursRepository::class)]
class CryptoCours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Devise::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Devise $devise = null;

    #[ORM\ManyToOne(targetEntity: Crypto::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Crypto $crypto = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 8)]
    private ?string $cours = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $datetime = null;

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

    public function getCrypto(): ?Crypto
    {
        return $this->crypto;
    }
    public function setId(int $crypto): self
    {
        $this->id = $crypto;
        return $this;
    }
    public function setCrypto(?Crypto $crypto): self
    {
        $this->crypto = $crypto;
        return $this;
    }

    public function getCours(): ?string
    {
        return $this->cours;
    }

    public function setCours(string $cours): self
    {
        $this->cours = $cours;
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
    public function generateRandomCours(float $min = 1000, float $max = 50000): float
    {
        return mt_rand($min * 100, $max * 100) / 100;
    }
}
?>