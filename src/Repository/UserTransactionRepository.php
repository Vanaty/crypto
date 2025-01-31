<?php

namespace App\Repository;

use App\Entity\UserTransaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends ServiceEntityRepository<UserTransaction>
 */
class UserTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserTransaction::class);
    }
    public function calculateUserBalance(int $idUser): float
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT sum(entre) - sum(sortie) AS balance
                FROM user_transaction 
                WHERE id_user = :idUser";

        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([ 'idUser' => $idUser]);
        return (float) $result->fetchOne();
    }

    //    /**
    //     * @return UserTransaction[] Returns an array of UserTransaction objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserTransaction
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function createUserTransaction($idUser, $entre,$sortie,$date, EntityManagerInterface $entityManager,$devise)
    {
        $userTransaction = new UserTransaction();
        $userTransaction->setIdUser($idUser);
        $userTransaction->setEntre($entre);
        $userTransaction->setDevise($devise);
        $userTransaction->setSortie($sortie);
        $userTransaction->setDatetime($date);  
        $entityManager->persist($userTransaction);
        $entityManager->flush();
    }
}
