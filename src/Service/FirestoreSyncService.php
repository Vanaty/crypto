<?php
namespace App\Service;

use App\Entity\CryptoCours;
use App\Entity\CryptoTransaction;
use App\Entity\Devise;
use App\Entity\UserTransaction;
use App\Repository\CryptoCoursRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use MrShan0\PHPFirestore\Attributes\FirestoreDeleteAttribute;
use MrShan0\PHPFirestore\Fields\FirestoreTimestamp;
use MrShan0\PHPFirestore\FirestoreClient;

class FirestoreSyncService
{
    private FirestoreClient $firestore;
    private EntityManagerInterface $em;
    private CryptoCoursRepository $ccR;

    public function __construct(FirebaseService $firebaseService, EntityManagerInterface $em, CryptoCoursRepository $ccR)
    {
        $this->firestore = $firebaseService->getFirestore();
        $this->em = $em;
        $this->ccR = $ccR;
    }

    public function syncCryptoCoursToFirestore(CryptoCours $cc): ?string
    {
        $data = [
            'cours' => $cc->getCours(),
            'crypto' => $cc->getCrypto()->getId(),
            'daty' => new FirestoreTimestamp($cc->getDatetime()),
            'devise' => $cc->getDevise()->getId()
        ];

        $docRef = $this->firestore->addDocument('CryptoCours',$data);

        return null;
    }

    public function syncCTrsToFirebase(CryptoTransaction $ctrs) {
        $data = [
            'id' => $ctrs->getId(),
            'cours'=> $ctrs->getCryptoCours(),
            'crypto'=> $ctrs->getCrypto()->getId(),
            'daty'=> new FirestoreTimestamp($ctrs->getDatetime()),
            'devise'=> $ctrs->getDevise()->getId(),
            'entre'=> $ctrs->getEntre(),
            'sortie'=> $ctrs->getSortie(),
            'user'=> $ctrs->getIdUser(),
        ];
        $this->firestore->addDocument('CryptoTransaction',$data);
    }


    public function syncUserTrsToFirebase(UserTransaction $ctrs) {
        $data = [
            'id' => $ctrs->getId(),
            'etat'=> $ctrs->getEtat(),
            'daty'=> new FirestoreTimestamp($ctrs->getDatetime()),
            'devise'=> $ctrs->getDevise()->getId(),
            'entre'=> $ctrs->getEntre(),
            'sortie'=> $ctrs->getSortie(),
            'user'=> $ctrs->getIdUser(),
        ];
        $this->firestore->addDocument('UserTransaction',$data);
    }

    public function updateEtatUserTrs(UserTransaction $ctrs) {
        $documents = $this->listDocuments('UserTransaction','id', $ctrs->getId());
        if ($documents) {
            $path = $documents[0]->getRelativeName();
            $cleanPath = (substr($path, 0, 1) === '/') ? substr($path, 1) : $path;
            $this->firestore->updateDocument($cleanPath, [
                'etat' => strval($ctrs->getEtat())
            ], true);
        }
    }

    public function suncCTFromFirestore() {
        try {
            $documents = $this->getDocumentsSansId("CryptoTransaction");
            $documents = $documents['documents'];
            foreach ($documents as $doc) { 
                $ut = new CryptoTransaction();
                $ut->setIdUser((int) $doc->get('user'));
                $devise = new Devise();
                $devise->setId((int) $doc->get('devise'));
                $ut->setDevise($devise);
                $ut->setDatetime(new DateTime(($doc->get('daty')->parseValue())));
                $ut->setEntre($doc->get('entre'));
                $ut->setSortie($doc->get('sortie'));
                $ut->setCryptoCours($doc->get('cours'));
                $this->em->persist($ut);
                $this->em->flush();

                $path = $doc->getRelativeName();
                $cleanPath = (substr($path, 0, 1) === '/') ? substr($path, 1) : $path;
                $this->firestore->updateDocument($cleanPath, [
                    'id' => $ut->getId()
                ]);
            }

        } catch (\Throwable $e) {
            throw new \Exception("Erreur Firebase: " . $e->getMessage());
        }
    }

    public function suncUTFromFirestore() {
        try {
            $documents = $this->getDocumentsSansId("UserTransaction");
            $documents = $documents['documents'];
            $devise = $this->em->getRepository(Devise::class)->find("1");
            foreach ($documents as $doc) {
                $ut = new UserTransaction();
                $ut->setIdUser((int) $doc->get('user'));
                $ut->setDevise($devise);
                $ut->setDatetime(new DateTime(($doc->get('daty')->parseValue())));
                $ut->setEntre($doc->get('entre'));
                $ut->setSortie($doc->get('sortie'));
                $ut->setEtat($doc->get('etat'));
                $this->em->persist($ut);
                $this->em->flush();

                $path = $doc->getRelativeName();
                $cleanPath = (substr($path, 0, 1) === '/') ? substr($path, 1) : $path;
                $this->firestore->updateDocument($cleanPath, [
                    'id' => $ut->getId()
                ]);
            }

        } catch (\Throwable $e) {
            throw new \Exception("Erreur Firebase: " . $e->getMessage());
        }
    }

    public function listDocuments(string $collection,string $field, $value) {
        $documents = $this->firestore->listDocuments($collection);
        $docs = [];

        if (isset($documents['documents'])) {
            foreach ($documents['documents'] as $doc) {
                if ($doc->get($field) == $value) {
                    $docs[] = $doc;
                }
            }
        }

        return array_merge($docs, [
            'documents' => $documents,
        ]);
    }


    public function getDocumentsSansId(string $collection) {
        $documents = $this->firestore->listDocuments($collection);
        $docs = [];

        if (isset($documents['documents'])) {
            foreach ($documents['documents'] as $doc) {
                if (!$doc->has("id")) {
                    $docs[] = $doc;
                }
            }
        }

        return [
            'documents' => $docs
        ];
    }
}
