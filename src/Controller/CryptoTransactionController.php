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

class CryptoTransactionController extends AbstractController
{
    private $entityManager;
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }
    #[Route('/CryptoTransaction', name: 'achatvente', methods: ['POST'])]
    public function createTransaction(
        Request $request,
        CryptoTransactionRepository $cryptoTransactionRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // Récupérer les données JSON de la requête
        $data = json_decode($request->getContent(), true);

        // Valider les données
        if (!isset($data['userId']) || !isset($data['cryptoId']) || !isset($data['type']) || !isset($data['amount'])) {
            return new JsonResponse(['error' => 'Données manquantes'], 400);
        }
        // Extraire les données
        $idUser = $data['userId'];
        $idCrypto = $data['cryptoId'];
        $type = $data['type'];
        $amount = $data['amount'];
        $date = $data['timestamp'];
        $userTransaction= new UserTransaction();
        $crypto = $entityManager->getRepository(Crypto::class)->find($idCrypto);
        $cours = $this->entityManager->getRepository( CryptoTransaction::class)->getCours($crypto);
        $data1 = [];
        $devise = $this->entityManager->getRepository(Devise::class)->find(1);
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
                $this->entityManager->getRepository( UserTransaction::class)->createUserTransaction($data['userId'], 0,$amount * $cours,(new \DateTime())->setTimestamp($data['timestamp']/1000),$entityManager,$devise);
                // Retourner une réponse JSON de succès
                $data1 = ['message' => 'Transaction créée avec succès',
                    'transaction_id' => $transactionId,];
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
                'transaction_id' => $transactionId,];   
            }
            return new JsonResponse($data1,200);
        } catch (\Exception $e) {
            // Retourner une réponse JSON en cas d'erreur
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
