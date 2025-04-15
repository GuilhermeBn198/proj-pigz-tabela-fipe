<?php
namespace App\Command;

use App\Service\FipeSyncService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'fipe:sync',
    description: 'Sincroniza dados da Tabela FIPE com o banco de dados'
)]
class FipeSyncCommand extends Command
{
    public function __construct(private FipeSyncService $fipeSyncService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Sincronização FIPE iniciada');

        try {
            $summary = $this->fipeSyncService->syncAll();
            foreach ($summary as $tipo => $dados) {
                $io->section("Tipo: $tipo");
                $io->listing([
                    "Marcas criadas: {$dados['brands_created']}",
                    "Modelos criados: {$dados['models_created']}",
                    "Anos criados: {$dados['years_created']}",
                ]);
            }
            $io->success('Sincronização concluída com sucesso.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Erro durante sincronização: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}