<?php
namespace App\Controller;

use App\Entity\Crypto;
use App\Entity\CryptoCours;
use App\Entity\Devise;
use App\Repository\ConfigDeviseRepository;
use App\Repository\CryptoRepository;
use App\Repository\DeviseRepository;
use App\Service\CryptoCoursService;
use App\Service\CryptoDataService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CryptoController extends BaseController
{
    private $cryptoRepository;
    private $deviseRepository;
    private ConfigDeviseRepository $configDeviseRepository;
    private $entityManager;
    private CryptoDataService $cryptoDataService;

    public function __construct(
        CryptoRepository $cryptoRepository,
        DeviseRepository $deviseRepository,
        ConfigDeviseRepository $configDeviseRepository,
        CryptoDataService $cryptoDataService,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
        $this->cryptoRepository = $cryptoRepository;
        $this->deviseRepository = $deviseRepository;
        $this->entityManager = $entityManager;
        $this->cryptoDataService = $cryptoDataService;
        $this->configDeviseRepository = $configDeviseRepository;
    }

    #[Route('/Random', name: 'random', methods: ['POST','GET'])]
    public function generateCryptoCours(Request $request): Response
    {
        // Récupérer l'ID de la devise envoyée par JSON
        $data = json_decode($request->getContent(), true);
        $deviseId = $data['deviseId'] ?? 1;  // Vérifier si la devise_id est bien présente

        if (!$deviseId) {
            return new Response('ID de la devise non spécifié.', Response::HTTP_BAD_REQUEST);
        }

        // Récupérer la devise à partir de l'ID
        $devise = $this->deviseRepository->find($deviseId);

        if (!$devise) {
            return new Response('Devise introuvable pour cet ID.', Response::HTTP_NOT_FOUND);
        }

        // Récupérer toutes les cryptos disponibles
        $cryptos = $this->cryptoRepository->findAll();

        // Vérifier qu'il y a au moins une crypto
        if (empty($cryptos)) {
            return new Response('Aucune crypto disponible pour générer les cours.', Response::HTTP_NOT_FOUND);
        }

        // Générer un cours aléatoire pour chaque crypto et la devise sélectionnée
        foreach ($cryptos as $crypto) {
            // Créer une nouvelle instance de CryptoCours
            $cryptoCours = new CryptoCours();
            $cryptoCours->setCrypto($crypto);
            $cryptoCours->setDevise($devise);
            $cryptoCours->setCours($cryptoCours->generateRandomCours());
            $cryptoCours->setDatetime(new \DateTime()); // Le cours est enregistré avec l'heure actuelle

            // Sauvegarder dans la base de données
            $this->entityManager->persist($cryptoCours);
        }

        // Valider toutes les persistances
        $this->entityManager->flush();

        return new Response('Cours générés pour toutes les cryptos avec la devise spécifiée.', Response::HTTP_OK);
    }

    #[Route('/cryptos', name: 'getAllCryptos', methods: ['GET'])]
    public function getAll(Request $request): Response
    {
        $token = $this->verifyToken($request);
        $data = json_decode($request->getContent(), true);
        $id = $data['id'] ?? null;
        $cryptoDataList = $this->cryptoDataService->getCryptoDataList();
        return $this->json($cryptoDataList);
    }

    #[Route('/devises', name: 'getAllDevises', methods: ['GET'])]
    public function getAllDevises(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $id = $data['id'] ?? null;
        $cryptoDataList = $this->configDeviseRepository->getAll();
        return $this->json($cryptoDataList);
    }

}
