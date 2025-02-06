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
use PSpell\Config;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserTransactionController extends AbstractController
{
    private $entityManager;
    private $serializer;
    private $validator;
    private $userTransactionRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UserTransactionRepository $userTransactionRepository
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->userTransactionRepository = $userTransactionRepository;
    }

    #[Route('/UserTransaction', name: 'UserTransaction', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
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
        return new JsonResponse([
            'message' => 'UserTransaction created successfully',
            'id' => $userTransaction->getId(),
        ], 201);
    }

    #[Route('/user/compte', name: 'userCompte', methods: ['GET'])]
    public function getUserBalance(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $idUser =$data['userId'];
        $idDevise =$data['idDevise'];
        
        if (!$idUser || !$idDevise) {
            return new JsonResponse(['error' => 'Missing parameters'], 400);
        }
        $devise = new Devise();
        $balance = $this->userTransactionRepository->calculateUserBalance($idUser);
        $configDevise = $this->entityManager->getRepository(ConfigDevise::class)->findBy(['devise' => $data['idDevise']],null,1,null);
        return new JsonResponse(['compte' => $devise->transformationAutre($configDevise[0]->getValeur(),$balance)], 201);
    }
    

}
