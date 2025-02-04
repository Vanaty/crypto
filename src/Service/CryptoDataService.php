<?php

namespace App\Service;

use App\Repository\CryptoRepository;
use App\Repository\CryptoCoursRepository;

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
            $cryptoDataList[] = [
                'id' => $crypto->getId(),
                'name' => $crypto->getNom(),
                'symbol' => $crypto->getSymbol(),
                'currentPrice' => $this->getLatestPrice($crypto->getId()),
                'priceHistory' => $this->getPriceHistory($crypto->getId()),
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
