<?php

namespace App\Redis;

use Redis;

class UserRedisHelper
{
    public function __construct(
        protected Redis $redis
    ) {}

    public function setLikesForUser($likes, $userId)
    {
        $cacheKey = "likes:user_$userId";
        $this->redis->sAdd($cacheKey, ...$likes);
    }
}