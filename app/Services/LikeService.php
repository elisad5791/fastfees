<?php
namespace App\Services;

use App\Redis\UserRedisHelper;
use App\Repositories\UserRepository;
use Redis;
use PDO;

class LikeService
{
    public function __construct(
        protected PDO $pdo, 
        protected Redis $redis,
        protected UserRepository $userRepository,
        protected UserRedisHelper $userRedisHelper
    ) {}
    
    public function runReceiver()
    {
        while (true) {
            $dataJson = $this->redis->brPop('queue_like', 0);
            $data = json_decode($dataJson[1], true); 
            
            $userId = $data['user_id'];
            $newsId = $data['news_id'];

            $this->addLog($userId, $newsId);
        }
    }

    public function addLog($userId, $newsId)
    {
        $message = "User $userId add like to news $newsId";
        $sql = "INSERT INTO like_logs(message) VALUES(?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$message]);
    }

    public function addLike($userId, $newsId)
    {
        $like = $this->userRepository->getLike($userId, $newsId);
        if (!empty($like)) {
            return;
        }

        $this->userRepository->addLike($userId, $newsId);
        $this->userRedisHelper->addLike($userId, $newsId);
    }

    public function removeLike($userId, $newsId)
    {
        $like = $this->userRepository->getLike($userId, $newsId);
        if (empty($like)) {
            return;
        }

        $this->userRepository->removeLike($userId, $newsId);
        $this->userRedisHelper->removeLike($userId, $newsId);
    }
}
