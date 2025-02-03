<?php

namespace App\Repository;

use App\Entity\CryptoTransaction;
use App\Entity\Crypto;
use App\Entity\Devise;
use App\Entity\CryptoCours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<CryptoTransaction>
 */
class CryptoTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CryptoTransaction::class);
    }

    //    /**
    //     * @return CryptoTransaction[] Returns an array of CryptoTransaction objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?CryptoTransaction
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
   
    public function getCours(Crypto $crypto): float
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
            SELECT cours
            FROM crypto_cours 
            WHERE crypto_id = :idCrypto
            ORDER BY datetime DESC
            LIMIT 1
        ";
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery([
            'idCrypto' => $crypto->getId(),
        ]);
        return (float) $result->fetchOne();
    }
    public function createTransaction(int $idUser, int $idCrypto, float $entre,$sortie,$idDeviseBase,$date, EntityManagerInterface $entityManager): int
    {
        $crypto = $entityManager->getRepository(Crypto::class)->find($idCrypto);
        $cours = $this->getCours($crypto);
        $devise = $entityManager->getRepository(Devise::class)->find($idDeviseBase);
        $transaction = new CryptoTransaction();
        $transaction->setIdUser($idUser);
        $transaction->setCrypto($crypto);
        $transaction->setCryptoCours($cours);
        $transaction->setEntre($entre); // Quantité de crypto achetée
        $transaction->setSortie($sortie); // Aucune sortie pour une transaction d'achat
        $transaction->setDevise($devise);
        $transaction->setDatetime((new \DateTime())->setTimestamp($date/1000));
        $entityManager->persist($transaction);
        $entityManager->flush();
        return $transaction->getId();
    }
    public function findByUserIdFromView(int $idUser): array
    {
        $entityManager = $this->getEntityManager();

        // Requête SQL brute pour interroger la vue
        $sql = '
            SELECT *
            FROM crypto_transaction_view
            WHERE id_user = :idUser
        ';
        // Exécuter la requête SQL
        $statement = $entityManager->getConnection()->prepare($sql);
        $result = $statement->executeQuery(['idUser' => $idUser]);

        // Retourner les résultats sous forme de tableau
        return $result->fetchAllAssociative();
    }


}
