<?php
namespace App\Service;
 
use MrShan0\PHPFirestore\FirestoreClient;
use MrShan0\PHPFirestore\Fields\FirestoreObject;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FirebaseService
{
    private FirestoreClient $firestore;

    public function __construct(string $apiKey,string $projectId)
    {
        $this->firestore = new FirestoreClient($projectId,$apiKey);
    }

    public function getFirestore(): FirestoreClient
    {
        return $this->firestore;
    }

}
