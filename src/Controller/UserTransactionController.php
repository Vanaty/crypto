<?php

namespace App\Controller;

use App\Entity\ConfigDevise;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserTransactionRepository;
use App\Entity\UserTransaction;
use App\Entity\Devise;
use App\Repository\CryptoTransactionRepository;
use App\Service\FirestoreSyncService;
use PSpell\Config;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTransactionController extends BaseController
{
    private $entityManager;
    private $serializer;
    private $validator;
    private $firestore;
    private $userTransactionRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserTransactionRepository $userTransactionRepository,
        FirestoreSyncService $firestore
    ) {
        parent::__construct($entityManager);
        $this->firestore = $firestore;
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->userTransactionRepository = $userTransactionRepository;
    }

    #[Route('/UserTransaction', name: 'UserTransaction', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $token = $this->verifyToken($request);
        $data = json_decode($request->getContent(), true);
        $userTransaction = new UserTransaction();
        $userTransaction->setIdUser($data['userId']);
        $userTransaction->setEntre($data['entre']);
        $userTransaction->setSortie($data['sortie']);
        $userTransaction->setEtat(1);
        $userTransaction->setDatetime((new \DateTime())->setTimestamp($data['timestamp']/1000));
        $devise = $this->entityManager->getRepository(Devise::class)->find($data['deviseId']);
        if (!$devise) {
            return new JsonResponse(['error' => 'Devise not found'], 404);
        }
        $userTransaction->setDevise($devise);
        $errors = $this->validator->validate($userTransaction);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], 400);
        }
        $configDevise = $this->entityManager->getRepository(ConfigDevise::class)->findBy(['devise' => $data['deviseId']],null,1,null);
        $userTransaction->setEntre($devise->transformationEuro($configDevise[0]->getValeur(),$userTransaction->getEntre()));
        $userTransaction->setSortie($devise->transformationEuro($configDevise[0]->getValeur(),$userTransaction->getSortie()));  
        $this->entityManager->persist($userTransaction);
        $this->entityManager->flush();
        $this->firestore->syncUserTrsToFirebase($userTransaction);
        return new JsonResponse([
            'message' => 'UserTransaction created successfully',
            'id' => $userTransaction->getId(),
        ], 200);
    }

    
    #[Route('/user/compte', name: 'userCompte', methods: ['GET'])]
    public function getUserBalance(Request $request, UserTransactionRepository $userTransactionRepository,CryptoTransactionRepository $ctr, EntityManagerInterface $entityManager): JsonResponse
    {
        $token = $this->verifyToken($request);
        $idUser = $request->query->get('userId');
        $idDevise = $request->query->get('idDevise');

        if (empty($idUser) || empty($idDevise)) {
            return new JsonResponse(['error' => 'Missing parameters'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Récupération du solde utilisateur
        $balance = $userTransactionRepository->calculateUserBalance($idUser);

        // Récupération de la configuration de la devise
        $configDevise = $entityManager->getRepository(ConfigDevise::class)->findOneBy(['devise' => $idDevise]);

        if (!$configDevise) {
            return new JsonResponse(['error' => 'Currency not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        // Transformation du solde
        $devise = new Devise();
        $transformedBalance = $devise->transformationAutre($configDevise->getValeur(), $balance);
        $cryptos = $ctr->getUserCrypto($entityManager,$idUser);

        return new JsonResponse(['usdBalance' => $transformedBalance, "cryptoHoldings" => $cryptos], JsonResponse::HTTP_OK);
    }
    
    #[Route('/user/{id}/transactions', name: 'userTransactions', methods: ['GET'])]
    public function getUserTransactions(Request $request, int $id): JsonResponse
    {
        $token = $this->verifyToken($request);
        $datas =  $this->userTransactionRepository->findBy(["idUser"=>$id]);
        $reps = [];
        foreach ($datas as $trs) {
            $reps[] = $trs->toTransactionJson();
        }
        return new JsonResponse($reps, JsonResponse::HTTP_OK);
    }


    #[Route('/transaction/{id}', name: 'get_transaction', methods: ['GET'])]
    public function getTransaction(UserTransactionRepository $repo, int $id,Request $request): JsonResponse
    {
        $token = $this->verifyToken($request);
        $transaction = $repo->find($id);
        
        if (!$transaction) {
            return new JsonResponse(['error' => 'Transaction not found'], 404);
        }

        return new JsonResponse($transaction->toTransactionJson());
    }
}
