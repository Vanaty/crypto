<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

abstract class BaseController extends AbstractController
{
    protected EntityManagerInterface $entityManagerdddd;

    public function __construct(EntityManagerInterface $bobota)
    {
        $this->entityManagerdddd = $bobota;
    }

    protected function verifyToken(Request $request): array
    {
        $tokenValue = $request->headers->get('Authorization');
        if ($tokenValue && preg_match('/^Bearer\s+(.+)$/', $tokenValue, $matches)) {
            $tokenValue = $matches[1]; // Retourne uniquement le token
        }
        if (!$tokenValue) {
            throw new UnauthorizedHttpException('Bearer', 'Token is missing');
        }
        $conn = $this->entityManagerdddd->getConnection();
        $sql = "SELECT id_token, expiration, active FROM Token WHERE token = :token";
        $stmt = $conn->prepare($sql);
        $result = $stmt->executeQuery(['token' => $tokenValue]);

        $token = $result->fetchAssociative();

        if (!$token) {
            throw new UnauthorizedHttpException('Bearer', 'Invalid token');
        }

        if (!$token['active']) {
            throw new UnauthorizedHttpException('Bearer', 'Token is inactive');
        }

        return $token;
    }
}
