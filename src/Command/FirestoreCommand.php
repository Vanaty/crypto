<?php
namespace App\Command;

use App\Service\FirestoreSyncService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:sync-firestore')]
class FirestoreCommand extends Command
{
    private FirestoreSyncService $syncService;

    public function __construct(FirestoreSyncService $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->syncService->suncUTFromFirestore();
        $this->syncService->suncCTFromFirestore();
        $output->writeln("Synchronisation Firestore → PostgreSQL terminée !");
        return Command::SUCCESS;
    }
}
