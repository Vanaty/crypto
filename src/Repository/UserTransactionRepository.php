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
        $userTransaction->setEtat(11);  
        $entityManager->persist($userTransaction);
        $entityManager->flush();
    }
    public function updateEtat(int $idTransaction, int $newEtat): void
    {
        // Récupérer l'EntityManager
        $entityManager = $this->getEntityManager();

        // Trouver la transaction par son ID
        $transaction = $this->find($idTransaction);

        if ($transaction) {
            // Mettre à jour l'état
            $transaction->setEtat($newEtat);

            // Persister les changements
            $entityManager->persist($transaction);
            $entityManager->flush();
        } else {
            throw new \Exception("Transaction non trouvée avec l'ID : $idTransaction");
        }
    }
public function findByFilters(?\DateTimeInterface $dateTimemax,?\DateTimeInterface $dateTimemin)
{
    // Récupérer l'EntityManager
    $entityManager = $this->getEntityManager();

    // Créer la base de la requête SQL
    $sql = "SELECT * 
            FROM user_transaction t
            WHERE 1=1"; // Remplacez 'crypto_transaction_view' par le nom réel de votre vue

    // Ajoutez un filtre pour la date/heure si nécessaire
    if ($dateTimemin && $dateTimemax) {
        $sql .= " AND t.datetime >= :dateTimemin  AND t.datetime <= :dateTimemax";
    }
    // Préparez la requête SQL native
    $query = $entityManager->getConnection()->prepare($sql);

    // Construire les paramètres de la requête
    $params = [];

    if ($dateTimemax) {
        // Formater le DateTime en chaîne
        $params['dateTimemax'] = $dateTimemax->format('Y-m-d H:i:s');
    }
    if ($dateTimemin) {
        // Formater le DateTime en chaîne
        $params['dateTimemin'] = $dateTimemin->format('Y-m-d H:i:s');
    }
    // Exécuter la requête avec les paramètres
    $result = $query->executeQuery($params);

    // Récupérer et retourner les résultats sous forme de tableau associatif
    return $result->fetchAllAssociative();
}
public function findAll(): array
    {
        $entityManager = $this->getEntityManager();

        // Requête SQL brute pour interroger la vue
        $sql = '
            SELECT *
            FROM user_transaction
        ';
        // Exécuter la requête SQL
        $statement = $entityManager->getConnection()->prepare($sql);
        $result = $statement->executeQuery();

        // Retourner les résultats sous forme de tableau
        return $result->fetchAllAssociative();
    }
    public function findByUserId(int $idUser): array
    {
        $entityManager = $this->getEntityManager();

        // Requête SQL brute pour interroger la vue
        $sql = '
            SELECT *
            FROM user_transaction
            WHERE id_user = :idUser
        ';
        // Exécuter la requête SQL
        $statement = $entityManager->getConnection()->prepare($sql);
        $result = $statement->executeQuery(['idUser' => $idUser]);

        // Retourner les résultats sous forme de tableau
        return $result->fetchAllAssociative();
    }

}
