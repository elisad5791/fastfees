<?php
namespace App\Services;

use App\Redis\NewsRedisHelper;
use App\Redis\UserRedisHelper;

class AdminService
{
    public function __construct(
        protected UserRedisHelper $userRedisHelper,
        protected NewsRedisHelper $newsRedisHelper,
    ) {}

    public function getLastDayUsers(): int
    {
        $count = $this->userRedisHelper->getLastDayUsers();
        return $count;
    }

    public function getLastHourUsers(): int
    {
        $count = $this->userRedisHelper->getLastHourUsers();
        return $count;
    }

    public function getPopularCategories(): array
    {
        $popularCategories = $this->newsRedisHelper->getPopularCategories();
        return $popularCategories;
    }

    public function getPopularTags(): array
    {
        $popularTags = $this->newsRedisHelper->getPopularTags();
        return $popularTags;
    }

    public function getPopularNews(): array
    {
        $popularNews = $this->newsRedisHelper->getPopularNews();
        return $popularNews;
    }
}
