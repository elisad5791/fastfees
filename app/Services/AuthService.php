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

    public function setUser($name)
    {
        $user = $this->userRepository->getUserByName($name);
        $userId = $user['id'] ?? null;

        if (empty($user)) {
            $userId = $this->userRepository->createUser($name);
        }

        $this->sessionHelper->setValues([
            ['key' => 'username', 'value' => $name],
            ['key' => 'userid', 'value' => $userId],
        ]);
        
        return $userId;
    }

    public function setLikes($userId)
    {
        $likes = $this->userRepository->getLikes($userId);

        if (!empty($likes)) {
            $this->userRedisHelper->setLikesForUser($likes, $userId);
        }
    }
}
