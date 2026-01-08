<?php

namespace App\Console;

use App\Redis\SearchRedisHelper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'search:index', description: 'Создание индекса в Redis для поиска по новостям')]
class SearchIndexCommand extends Command
{
    public function __construct(protected SearchRedisHelper $searchRedisHelper)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        try {
            $io->title('Создание индекса в Redis для поиска по новостям');
            
            $this->searchRedisHelper->createIndex();
            
            $io->success('Индекс создан.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Критическая ошибка: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}