<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CryptoTransactionRepository;
#[ORM\Entity(repositoryClass: CryptoTransactionRepository::class)]
class CryptoTransaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $idUser = null;

    #[ORM\ManyToOne(targetEntity: Crypto::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Crypto $crypto = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 8)]
    private ?string $cryptoCours = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 8)]
    private ?string $entre = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 8)]
    private ?string $sortie = null;
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $datetime = null;


    #[ORM\ManyToOne(targetEntity: Devise::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Devise $devise = null;

    // Getters and Setters
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
    public function getCryptoCours(): ?string
    {
        return $this->cryptoCours;
    }

    public function setCryptoCours(string $crypto): self
    {
        $this->cryptoCours = $crypto;
        return $this;
    }
    public function getCrypto(): ?Crypto
    {
        return $this->crypto;
    }

    public function setCrypto(?Crypto $crypto): self
    {
        $this->crypto = $crypto;
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
