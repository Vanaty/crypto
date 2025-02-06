<?php
namespace App\Repository;

use App\Entity\Admin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AdminRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Admin::class);
    }

    public function validateLogin(string $userName, string $motDepasse): ?Admin
    {
        return $this->createQueryBuilder('a')
            ->where('a.userName = :userName')
            ->andWhere('a.motDepasse = :motDepasse')
            ->setParameter('userName', $userName)
            ->setParameter('motDepasse', $motDepasse)
            ->getQuery()
            ->getOneOrNullResult(); // ✅ Retourne NULL si aucun admin trouvé
    }
}
