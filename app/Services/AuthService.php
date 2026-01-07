<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Session\SessionHelper;
use App\Redis\UserRedisHelper;

class AuthService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected SessionHelper $sessionHelper,
        protected UserRedisHelper $userRedisHelper
    ) {}

    public function setUser(string $name): int
    {
        $user = $this->userRepository->getUserByName($name);
        $userId = $user['id'] ?? null;

        if (empty($user)) {
            $userId = $this->userRepository->createUser($name);
        }

        $isAdmin = $user['is_admin'] == 1;
        $this->sessionHelper->setValues([
            ['key' => 'username', 'value' => $name],
            ['key' => 'userid', 'value' => $userId],
            ['key' => 'admin', 'value' => $isAdmin],
        ]);
        
        return $userId;
    }

    public function setLikes(int $userId): void
    {
        $likes = $this->userRepository->getLikes($userId);

        if (!empty($likes)) {
            $this->userRedisHelper->setLikesForUser($likes, $userId);
        }
    }
}
