<?php
namespace App\Service;

use App\Repository\CryptoRepository;
use App\Repository\CryptoCoursRepository;
use DateTime;
use DateInterval;

class CryptoDataService
{
    private CryptoRepository $cryptoRepository;
    private CryptoCoursRepository $cryptoCoursRepository;

    public function __construct(CryptoRepository $cryptoRepository, CryptoCoursRepository $cryptoCoursRepository)
    {
        $this->cryptoRepository = $cryptoRepository;
        $this->cryptoCoursRepository = $cryptoCoursRepository;
    }

    public function getCryptoDataList(): array
    {
        $cryptos = $this->cryptoRepository->findAll();
        $cryptoDataList = [];

        foreach ($cryptos as $crypto) {
            $latestPrice = $this->getLatestPrice($crypto->getId());
            $price24hAgo = $this->getPrice24hAgo($crypto->getId());
            $change24h = $price24hAgo ? (($latestPrice - $price24hAgo) / $price24hAgo) * 100 : 0;

            $cryptoDataList[] = [
                'id' => $crypto->getId(),
                'name' => $crypto->getNom(),
                'symbol' => $crypto->getSymbol(),
                'currentPrice' => $latestPrice,
                'priceHistory' => $this->getPriceHistory($crypto->getId()),
                'change24h' => round($change24h, 2),
            ];
        }

        return $cryptoDataList;
    }

    private function getLatestPrice(int $cryptoId): ?float
    {
        $latestCours = $this->cryptoCoursRepository->findOneBy(
            ['crypto' => $cryptoId],
            ['datetime' => 'DESC']
        );

        return $latestCours ? (float) $latestCours->getCours() : null;
    }

    private function getPrice24hAgo(int $cryptoId): ?float
    {
        $date24hAgo = (new DateTime())->sub(new DateInterval('P1D'));

        $cours24hAgo = $this->cryptoCoursRepository->createQueryBuilder('c')
            ->where('c.crypto = :crypto')
            ->andWhere('c.datetime <= :date')
            ->setParameter('crypto', $cryptoId)
            ->setParameter('date', $date24hAgo)
            ->orderBy('c.datetime', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if(!$cours24hAgo) {
            $cours24hAgo = $this->cryptoCoursRepository->createQueryBuilder('c')
                ->where('c.crypto = :crypto')
                ->setParameter('crypto', $cryptoId)
                ->orderBy('c.datetime', 'ASC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        }
        return $cours24hAgo ? (float) $cours24hAgo->getCours() : null;
    }

    private function getPriceHistory(int $cryptoId): array
    {
        $historicalData = $this->cryptoCoursRepository->findBy(
            ['crypto' => $cryptoId],
            ['datetime' => 'ASC']
        );

        $history = [];
        foreach ($historicalData as $data) {
            $history[] = [
                'date' => $data->getDatetime()->format('Y-m-d H:i:s'),
                'price' => (float) $data->getCours(),
            ];
        }

        return $history;
    }
}
