<?php
namespace App\Service;

use App\Entity\CryptoCours;
use App\Entity\Devise;
use App\Repository\CryptoRepository;
use App\Repository\CryptoCoursRepository;
use DateTime;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;

class CryptoCoursService
{
    private CryptoRepository $cryptoRepository;
    private CryptoCoursRepository $cryptoCoursRepository;
    private FirestoreSyncService $firestore;
    private EntityManagerInterface $em;

    public function __construct(
        CryptoRepository $cryptoRepository, 
        CryptoCoursRepository $cryptoCoursRepository,
        FirestoreSyncService $s, 
        EntityManagerInterface $em)
    {
        $this->cryptoRepository = $cryptoRepository;
        $this->cryptoCoursRepository = $cryptoCoursRepository;
        $this->em = $em;
        $this->firestore = $s;
    }

    public function generateRandom() {
        $devise = $this->em->getRepository(Devise::class)->find(1);
        $cryptos = $this->cryptoRepository->findAll();
        
        foreach ($cryptos as $crypto) {
            $cryptoCours = new CryptoCours();
            $cryptoCours->setCrypto($crypto);
            $cryptoCours->setDevise($devise);
            $cryptoCours->setCours($cryptoCours->generateRandomCours());
            $cryptoCours->setDatetime(new \DateTime()); // Le cours est enregistré avec l'heure actuelle
            $this->firestore->syncCryptoCoursToFirestore($cryptoCours);
            // Sauvegarder dans la base de données
            $this->em->persist($cryptoCours);
        }

        // Valider toutes les persistances
        $this->em->flush();
    }
}