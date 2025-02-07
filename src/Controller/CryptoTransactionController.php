<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CryptoTransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UserTransaction;
use App\Entity\Devise;
use App\Entity\ConfigDevise;
use App\Entity\Crypto;
use App\Entity\CryptoTransaction;

class CryptoTransactionController extends BaseController
{
    private $entityManager;
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
        $this->entityManager = $entityManager;
    }
    #[Route('/CryptoTransaction', name: 'achatvente1', methods: ['POST'])]
    public function createTransaction(
        Request $request,
        CryptoTransactionRepository $cryptoTransactionRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Récupérer les données JSON de la requête
        $token = $this->verifyToken($request);
        $data = json_decode($request->getContent(), true);

        // Valider les données
        if (!isset($data['userId']) || !isset($data['cryptoId']) || !isset($data['type']) || !isset($data['amount'])) {
            return new JsonResponse(['error' => 'Données manquantes'], 400);
        }
        // Extraire les données
        $statut = 200;
        $idUser = $data['userId'];
        $idCrypto = $data['cryptoId'];
        $type = $data['type'];
        $amount = $data['amount'];
        $date = $data['timestamp'];
        $crypto = $entityManager->getRepository(Crypto::class)->find($idCrypto);
        $cours = $this->entityManager->getRepository( CryptoTransaction::class)->getCours($crypto);
        $data1 = [];
        $devise = $this->entityManager->getRepository(Devise::class)->find(1);
        $balance = $this->entityManager->getRepository(UserTransaction::class)->calculateUserBalance($idUser);
        // Appeler la fonction createTransaction
        try {
            if($type=='buy'){
                $transactionId = $cryptoTransactionRepository->createTransaction(
                    $idUser,
                    $idCrypto,
                    $amount,
                    0,
                    1,
                    $date,
                    $entityManager
                );
                if ($balance -($amount * $cours) < 0) {
                    $data1 = ['message' => 'Transaction impossible','error' => 'montant insuffisant','data' => null];
                    $statut = 500;    
                }
                else{
                    $this->entityManager->getRepository( UserTransaction::class)->createUserTransaction($data['userId'], 0,$amount * $cours,(new \DateTime())->setTimestamp($data['timestamp']/1000),$entityManager,$devise);
                    $data1 = ['message' => 'Transaction créée avec succès',
                    'transaction_id' => $transactionId,'error'=> null];
                    $statut = 200;
                }
                
            }
            if($type=='sell'){
                $transactionId = $cryptoTransactionRepository->createTransaction(
                    $idUser,
                    $idCrypto,
                    0,
                    $amount,
                    1,
                    $date,
                    $entityManager
                );
                $this->entityManager->getRepository( UserTransaction::class)->createUserTransaction($data['userId'], $amount * $cours,0,(new \DateTime())->setTimestamp($data['timestamp']/1000),$entityManager,$devise);
                // Retourner une réponse JSON de succès
                $data1 = ['message' => 'Transaction créée avec succès',
                'data' => $transactionId,'error'=> null];
                $statut = 200;   
            }
            return new JsonResponse($data1,$statut);
        } catch (\Exception $e) {
            // Retourner une réponse JSON en cas d'erreur
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    #[Route('/ListeCryptoTransaction', name: 'achatventecrypto', methods: ['GET'])]
    public function getTransaction(
        Request $request
    ): JsonResponse {
        $token = $this->verifyToken($request);
        $statut = 200;
        $idUser = $request->get("userId",null);
        if (!isset($idUser)) {
            $cours = $this->entityManager->getRepository( CryptoTransaction::class)->findAllView();        
            return new JsonResponse($cours, 200);    
        }
        else{
            $cours = $this->entityManager->getRepository( CryptoTransaction::class)->findByUserIdFromView($idUser);        
            return new JsonResponse($cours, 200);
        }
        
    }
    #[Route('/user/{idUser}/cryptos', name: 'get_cryptos_amount', methods: ['GET'])]
    public function getCryptoAmount(CryptoTransactionRepository $ctr,EntityManagerInterface $entityManager,int $idUser,$request): JsonResponse
    {
        $token = $this->verifyToken($request);
        $transaction = $ctr->getUserCrypto($entityManager,$idUser);

        if (!$transaction) {
            return new JsonResponse(['error' => 'Transaction not found'], 404);
        }

        return new JsonResponse($transaction);
    }
}
