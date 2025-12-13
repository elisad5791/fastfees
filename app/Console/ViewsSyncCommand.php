<?php

namespace App\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use PDO;
use Redis;

#[AsCommand(name: 'views:sync', description: 'Перенос данных просмотров из Redis в MySQL')]
class ViewsSyncCommand extends Command
{
    protected $pdo;
    protected $redis;

    public function __construct(PDO $pdo, Redis $redis)
    {
        parent::__construct();
        $this->pdo = $pdo;
        $this->redis = $redis;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        try {
            $io->title('Синхронизация просмотров из Redis в MySQL');
            
            $keys = $this->redis->keys('views:news_*');
            
            if (empty($keys)) {
                $io->success('Нет данных для синхронизации');
                return Command::SUCCESS;
            }
            
            $io->text(sprintf('Найдено %d записей для обработки', count($keys)));
            
            $data = [];
            foreach ($keys as $key) {
                $newsId = str_replace('views:news_', '', $key);
                $viewsCount = (int) $this->redis->get($key);
                
                if ($viewsCount > 0) {
                    $data[] = ['id' => $newsId, 'views' => $viewsCount];
                }
            }

            $this->updateViews($data);
            
            $io->success('Синхронизация завершена.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Критическая ошибка: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    private function updateViews($data): void
    {
        if (empty($data)) {
            return;
        }

        $sql = "TRUNCATE TABLE news_views";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $values = [];
        $params = [];

        foreach ($data as $item) {
            $values[] = "(?, ?)";
            $params[] = $item['id'];
            $params[] = $item['views'];
        }

        $sql = "INSERT INTO news_views (news_id, views) VALUES " . implode(", ", $values);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
}