<?php

namespace App\Repository;

use App\Entity\LiaisonCoursCryptomonnaie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder; 

/**
 * @extends ServiceEntityRepository<LiaisonCoursCryptomonnaie>
 */
class LiaisonCoursCryptomonnaieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LiaisonCoursCryptomonnaie::class);
    }

    //    /**
    //     * @return LiaisonCoursCryptomonnaie[] Returns an array of LiaisonCoursCryptomonnaie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?LiaisonCoursCryptomonnaie
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findLatestByCrypto($idCrypto)
    {
        return $this->createQueryBuilder('l')
            ->where('l.id_cryptomonnaie = :idCrypto')
            ->setParameter('idCrypto', $idCrypto)
            ->orderBy('l.dateUpdate', 'DESC') // Trier par date de mise à jour décroissante
            ->setMaxResults(1) // Limiter à une seule entrée
            ->getQuery()
            ->getOneOrNullResult(); // Retourne null s'il n'y a pas de résultat
    }
}
