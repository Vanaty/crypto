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
public function findByFilters(?\DateTimeInterface $dateTimemax,?\DateTimeInterface $dateTimemin, ?int $cryptoId)
{
    // Récupérer l'EntityManager
    $entityManager = $this->getEntityManager();

    // Créer la base de la requête SQL
    $sql = "SELECT * 
            FROM crypto_transaction_view t
            WHERE 1=1"; // Remplacez 'crypto_transaction_view' par le nom réel de votre vue

    // Ajoutez un filtre pour la date/heure si nécessaire
    if ($dateTimemin && $dateTimemax) {
        $sql .= " AND t.datetime >= :dateTimemin  AND t.datetime <= :dateTimemax";
    }

    // Ajoutez un filtre pour la crypto si nécessaire
    if ($cryptoId) {
        $sql .= " AND t.crypto_id = :cryptoId";
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

    if ($cryptoId) {
        $params['cryptoId'] = $cryptoId;
    }

    // Exécuter la requête avec les paramètres
    $result = $query->executeQuery($params);

    // Récupérer et retourner les résultats sous forme de tableau associatif
    return $result->fetchAllAssociative();
}


    

    public function findByUserIdFromView(int $idUser): array
    {
        $entityManager = $this->getEntityManager();

        // Requête SQL brute pour interroger la vue
        $sql = '
            SELECT crypto_id cryptoId,id_user userId, (entre - sortie) amount,crypto_cours as price,datetime timestamp,type 
            FROM crypto_transaction_view
            WHERE id_user = :idUser
            ORDER BY datetime DESC
        ';
        // Exécuter la requête SQL
        $statement = $entityManager->getConnection()->prepare($sql);
        $result = $statement->executeQuery(['idUser' => $idUser]);

        // Retourner les résultats sous forme de tableau
        $responses = $result->fetchAllAssociative() ;
        for ($i=0; $i < count($responses); $i++) { 
            $responses[$i]["price"] = floatval($responses[$i]["price"]);
            $responses[$i]["amount"] = floatval($responses[$i]["amount"]);
            $responses[$i]["userId"] = $responses[$i]["userid"];
            $responses[$i]["cryptoId"] = $responses[$i]["cryptoid"];
        }
        return $responses;
    }
    public function findAllView(): array
    {
        $entityManager = $this->getEntityManager();

        // Requête SQL brute pour interroger la vue
        $sql = '
            SELECT *
            FROM crypto_transaction_view
        ';
        // Exécuter la requête SQL
        $statement = $entityManager->getConnection()->prepare($sql);
        $result = $statement->executeQuery();

        // Retourner les résultats sous forme de tableau
        return $result->fetchAllAssociative();
    }

    public function getUserCrypto( EntityManagerInterface $entityManager,int $idUser) {
        $entityManager = $this->getEntityManager();

        // Requête SQL brute pour interroger la vue
        $sql = '
            SELECT c.id as id_crypto,COALESCE(sum(entre)  - sum(sortie),0) amount
            FROM crypto c
            LEFT JOIN crypto_transaction ct ON c.id=ct.crypto_id
            WHERE ct.id_user = :idUser
            GROUP BY c.id
        ';

        $statement = $entityManager->getConnection()->prepare($sql);
        $result = $statement->executeQuery(["idUser" => $idUser]);
        $res = $result->fetchAllAssociative();
        $cryptos = $entityManager->getRepository(Crypto::class)->findAll();
        $rep = [];
        foreach ($cryptos as $crypto) {
            $rep[$crypto->getId()]=0;
        }

        if ($res) {
            foreach ($res as $value) {
                $rep[$value["id_crypto"]] =  floatval($value["amount"]);
            }
        }
        return $rep;
    }
}
