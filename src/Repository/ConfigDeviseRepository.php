<?php

namespace App\Repository;

use App\Entity\ConfigDevise;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ConfigDevise>
 */
class ConfigDeviseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ConfigDevise::class);
    }

    //    /**
    //     * @return ConfigDevise[] Returns an array of ConfigDevise objects
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

    //    public function findOneBySomeField($value): ?ConfigDevise
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getAll(): array
    {
         $data = $this->createQueryBuilder('d')
            ->getQuery()
            ->getResult();
        $reps = [];
        foreach ($data as $devise) {
            $reps[] = array("id"=>$devise->getDevise()->getId(),"nom"=>$devise->getDevise()->getNom(),"valeur"=>$devise->getValeur());
        }
        return $reps;
    }
}
