<?php
namespace App\Command;

use App\Service\CryptoCoursService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:random-crypto')]
class CryptoCoursCommand extends Command
{
    private CryptoCoursService $ccService;

    public function __construct(CryptoCoursService $syncService)
    {
        parent::__construct();
        $this->ccService = $syncService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->ccService->generateRandom();
        $output->writeln("Generation crypto terminée !");
        return Command::SUCCESS;
    }
}
