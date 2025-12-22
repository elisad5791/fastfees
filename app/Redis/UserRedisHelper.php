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

    public function addLike($userId, $newsId)
    {
        $cacheKey = "likes:user_$userId";
        $this->redis->sAdd($cacheKey, $newsId);

        $cacheKey = 'likes:news';
        $this->redis->hIncrBy($cacheKey, $newsId, 1);

        $action = ['user_id' => $userId, 'news_id' => $newsId];
        $this->redis->lPush('queue_like', json_encode($action));
    }

    public function removeLike($userId, $newsId)
    {
        $cacheKey = "likes:user_$userId";
        $this->redis->sRem($cacheKey, $newsId); 

        $cacheKey = 'likes:news';
        $this->redis->hIncrBy($cacheKey, $newsId, -1);
    }
}