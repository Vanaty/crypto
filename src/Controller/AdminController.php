<?php
namespace App\Controller;

use App\Entity\Crypto;
use App\Repository\AdminRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\CryptoTransaction;
use App\Entity\UserTransaction;
use Doctrine\ORM\EntityManagerInterface;
class AdminController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/login', name: 'admin_login', methods: ['GET', 'POST'])]
    public function login(Request $request, AdminRepository $adminRepository): Response
    {
        if ($request->isMethod('POST')) {
            $userName = $request->request->get('userName');
            $motDepasse = $request->request->get('motDepasse');

            // Vérification dans la base de données
            $admin = $adminRepository->validateLogin($userName, $motDepasse);

            if (!$admin) {
                return $this->render('admin/login.html.twig', [
                    'error' => 'Identifiants incorrects'
                ]);
            }
            return $this->redirectToRoute('admin_dashboard'); // Redirection après connexion réussie
        }
        return $this->render('admin/login.html.twig', [
            'error' => null
        ]);
    }
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function dashboard(): Response
    {   
        return $this->render('admin/dashboard.html.twig');
    }
    #[Route('/transaction', name: 'admin_liste_crypto_transaction', methods: ['GET'])]
    public function getTransaction(Request $request): Response
    {
        $transactions = $this->entityManager->getRepository(CryptoTransaction::class)->findAllView();
        $crypto = $this->entityManager->getRepository(Crypto::class)->findAll();
        return $this->render('admin/dashboard.html.twig', [
            'transactions' => $transactions,
            'page' => 'admin/transaction.html.twig',
            'cryptos' => $crypto
        ]);
    }
    #[Route('/validation', name: 'admin_liste_user_depot_retrait', methods: ['GET'])]
    public function getValidation(Request $request): Response
    {
        $transactions = $this->entityManager->getRepository(UserTransaction::class)->findAll();
        $crypto = $this->entityManager->getRepository(Crypto::class)->findAll();
        return $this->render('admin/dashboard.html.twig', [
            'transactions' => $transactions,
            'page' => 'admin/validation.html.twig',
            'cryptos' => $crypto
        ]);
    }
    #[Route('/validationTransaction/{id}', name: 'validationTransaction')]
    public function validerDepotRetrait(Request $request,int $id)
    {
        $idUserTransaction =$id;
        $this->entityManager->getRepository(UserTransaction::class)->updateEtat($idUserTransaction,11);
        return $this->redirectToRoute('admin_liste_crypto_transaction');
    }
    #[Route('/validationByid/{id}', name: 'validationByid',methods: ['GET'])]
    public function validationByid(Request $request,$id): Response
    {
        $transactions = $this->entityManager->getRepository(UserTransaction::class)->findByUserId( $id); 
        $crypto = $this->entityManager->getRepository(Crypto::class)->findAll();
        return $this->render('admin/dashboard.html.twig', [
            'transactions' => $transactions,
            'page' => 'admin/validation.html.twig',
            'cryptos' => $crypto
        ]);
    }
    #[Route('/transactionByid/{id}', name: 'transactionByid',methods: ['GET'])]
    public function transactionByid(Request $request,$id): Response
    {
        $transactions = $this->entityManager->getRepository(CryptoTransaction::class)->findByUserIdFromView($id); 
        $crypto = $this->entityManager->getRepository(Crypto::class)->findAll();
        return $this->render('admin/dashboard.html.twig', [
            'transactions' => $transactions,
            'page' => 'admin/transaction.html.twig',
            'cryptos' => $crypto
        ]);
    }
#[Route('/filtreTransaction', name: 'filtreTransaction')]
public function index(Request $request)
{
    // Récupérer la date et l'heure sous forme de chaînes
    $datemin = $request->query->get('datemin');
    $heuremin = $request->query->get('heuremin');
    $datemax = $request->query->get('datemax');
    $heuremax = $request->query->get('heuremax');
    $cryptoId = $request->query->get('crypto');

    // Créer un objet DateTime à partir de la date et de l'heure si elles sont présentes
    if ($datemin && $heuremin) {
        // Combiner la date et l'heure en une seule chaîne
        $dateTimeString = $datemin . ' ' . $heuremin;
        $dateTimemin = new \DateTime($dateTimeString); // Créer un DateTime
    } 
    if($datemax && $heuremax) {
        $dateTimeString = $datemax . ' ' . $heuremax; // Si aucune date ou heure n'est fournie
        $dateTimemax = new \DateTime($dateTimeString);
    }
    if($datemin && !$heuremin){
        $dateTimeString = $datemin . ' ' . '00:00:00'; // Si aucune date ou heure n'est fournie
        $dateTimemin = new \DateTime($dateTimeString);
    }
    if($datemax && !$heuremax){
        $dateTimeString = $datemax . ' ' . '00:00:00'; // Si aucune date ou heure n'est fournie
        $dateTimemax = new \DateTime($dateTimeString);
    }
    if(!$datemax && $datemin){
        $dateTimemax = new \DateTime();
    }
    if(!$datemin && $datemax){
        $dateTimemin = new \DateTime();
    }
    if(!$datemin && !$datemax){
        $dateTimemax=null;
        $dateTimemin=null;
    }

    // Passer la dateTime combinée dans la fonction findByFilters
    $transactions = $this->entityManager->getRepository(CryptoTransaction::class)->findByFilters(
        $dateTimemax,
        $dateTimemin,
        $cryptoId ? (int) $cryptoId : null
    );

    $crypto = $this->entityManager->getRepository(Crypto::class)->findAll();

    return $this->render('admin/dashboard.html.twig', [
        'transactions' => $transactions,
        'cryptos' => $crypto,
        'page' => 'admin/transaction.html.twig'
    ]);
}
#[Route('/filtreValidation', name: 'filtreValidation')]
public function index1(Request $request)
{
    // Récupérer la date et l'heure sous forme de chaînes
    $datemin = $request->query->get('datemin');
    $heuremin = $request->query->get('heuremin');
    $datemax = $request->query->get('datemax');
    $heuremax = $request->query->get('heuremax');

    // Créer un objet DateTime à partir de la date et de l'heure si elles sont présentes
    if ($datemin && $heuremin) {
        // Combiner la date et l'heure en une seule chaîne
        $dateTimeString = $datemin . ' ' . $heuremin;
        $dateTimemin = new \DateTime($dateTimeString); // Créer un DateTime
    } 
    if($datemax && $heuremax) {
        $dateTimeString = $datemax . ' ' . $heuremax; // Si aucune date ou heure n'est fournie
        $dateTimemax = new \DateTime($dateTimeString);
    }
    if($datemin && !$heuremin){
        $dateTimeString = $datemin . ' ' . '00:00:00'; // Si aucune date ou heure n'est fournie
        $dateTimemin = new \DateTime($dateTimeString);
    }
    if($datemax && !$heuremax){
        $dateTimeString = $datemax . ' ' . '00:00:00'; // Si aucune date ou heure n'est fournie
        $dateTimemax = new \DateTime($dateTimeString);
    }
    if(!$datemax && $datemin){
        $dateTimemax = new \DateTime();
    }
    if(!$datemin && $datemax){
        $dateTimemin = new \DateTime();
    }
    if(!$datemin && !$datemax){
        $dateTimemax=null;
        $dateTimemin=null;
    }

    // Passer la dateTime combinée dans la fonction findByFilters
    $transactions = $this->entityManager->getRepository(UserTransaction::class)->findByFilters(
        $dateTimemax,
        $dateTimemin
    );
    return $this->render('admin/dashboard.html.twig', [
        'transactions' => $transactions,
        'page' => 'admin/validation.html.twig'
    ]);
}

}
