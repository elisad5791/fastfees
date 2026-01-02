<?php

namespace App\Redis;

use Redis;

class NewsRedisHelper
{
    public function __construct(
        protected Redis $redis
    ) {}

    public function getTagNews($tagId)
    {
        $cacheKey = "list:tag_$tagId";
        $news = $this->redis->get($cacheKey);

        return $news;
    }

    public function setTagNews($news, $tagId)
    {
        $cacheKey = "list:tag_$tagId";
        $this->redis->set($cacheKey, json_encode($news));
    }

    public function getTagTitle($tagId)
    {
        $cacheKey = "title:tag_$tagId";
        $title = $this->redis->get($cacheKey);

        return $title;
    }

    public function setTagTitle($tagTitle, $tagId)
    {
        $cacheKey = "title:tag_$tagId";
        $this->redis->set($cacheKey, $tagTitle);
    }

    public function getCategoryNews($categoryId)
    {
        $cacheKey = "list:category_$categoryId";
        $news = $this->redis->get($cacheKey);

        return $news;
    }

    public function setCategoryNews($news, $categoryId)
    {
        $cacheKey = "list:category_$categoryId";
        $this->redis->set($cacheKey, json_encode($news));
    }

    public function getCategoryTitle($categoryId)
    {
        $cacheKey = "title:category_$categoryId";
        $title = $this->redis->get($cacheKey);

        return $title;
    }

    public function setCategoryTitle($categoryTitle, $categoryId)
    {
        $cacheKey = "title:category_$categoryId";
        $this->redis->set($cacheKey, $categoryTitle);
    }

    public function getPopular()
    {
        $popular = $this->redis->zrevrange('news:top', 0, 2, ['WITHSCORES' => true]);
        return $popular;
    }

    public function getCityPopular(int $userId): array
    {
        $key = "city_popular:user_{$userId}";
        $data = $this->redis->get($key);
        if (!empty($data)) {
            $data = json_decode($data, true);
        } else {
            $data = [];
        }
        return $data;
    }

    public function setCityPopular(array $data, int $userId): void
    {
        if (empty($data) || empty($userId)) {
            return;
        }

        $key = "city_popular:user_{$userId}";
        $data = json_encode($data);
        $this->redis->set($key, $data);
        $this->redis->expire($key, 600);
    }

    public function getClosestUsers(int $userId): array
    {
        $key = 'userplaces';
        $options = ['count' => 10];
        $data = $this->redis->geoRadiusByMember($key, $userId, 50, 'km', $options);
        $closestUsers = array_values(array_filter($data, fn($item) => $item != $userId));
        return $closestUsers;
    }

    public function getItem($id)
    {
        $cacheKey = "item:news_$id";
        $item = $this->redis->get($cacheKey);

        return $item;
    }

    public function getNewsCount()
    {
        $cacheKey = 'list:count';
        $count = $this->redis->get($cacheKey);

        return $count;
    }

    public function setNewsCount($count)
    {
        $cacheKey = 'list:count';
        $this->redis->set($cacheKey, $count);
    }

    public function getNewsPage($page)
    {
        $cacheKey = "list:page_$page";
        $news = $this->redis->get($cacheKey);

        return $news;
    }

    public function setNewsPage($news, $page)
    {
        $cacheKey = "list:page_$page";
        $this->redis->set($cacheKey, json_encode($news));
    }

    public function getViews($id)
    {
        $views = $this->redis->zscore('news:top', $id);
        return $views;
    }

    public function getRecently(int $userId): array
    {
        $cacheKey = "recent:user_{$userId}";
        $data = $this->redis->lRange($cacheKey, 0, -1);

        $recently = [];
        foreach ($data as $item) {
            $recently[] = json_decode($item, true);
        }

        return $recently;
    }

    public function updateRecently(int $userId, array $shortItem): void
    {
        $key = "recent:user_{$userId}";
        $val = json_encode($shortItem);
        $this->redis->lPush($key, $val);
        $this->redis->lTrim($key, 0, 4);
        $this->redis->expire($key, 24 * 60 * 60);
    }

    public function getLikeCount($newsId)
    {
        $cacheKey = 'likes:news';
        $likeCount = $this->redis->hGet($cacheKey, $newsId);
        if (empty($likeCount)) {
            $likeCount = 0;
        }

        return $likeCount;
    }

    public function getCurrentLike($userId, $newsId)
    {
        $like = false;
        if (!empty($userId)) {
            $cacheKey = "likes:user_$userId";
            $likes = $this->redis->sMembers($cacheKey);
            $like = in_array($newsId, $likes);
        }

        return $like;
    }

    public function getViewsCount($newsId)
    {
        $viewsCount = $this->redis->zIncrBy('news:top', 1, $newsId);
        return $viewsCount;
    }

    public function getCategorySimilar($newsId)
    {
        $cacheKey = "category:similar:news_$newsId";
        $data = $this->redis->lRange($cacheKey, 0, -1);
        return $data;
    }

    public function setCategorySimilar($newsId, $news)
    {
        $cacheKey = "category:similar:news_$newsId";
        $val = json_encode($news);
        $this->redis->lPush($cacheKey, $val);
    }

    public function getTagSimilar($newsId)
    {
        $cacheKey = "tag:similar:news_$newsId";
        $data = $this->redis->zRevRange($cacheKey, 0, -1);
        return $data;
    }

    public function setTagSimilar($newsId, $count, $news)
    {
        $cacheKey = "tag:similar:news_$newsId";
        $val = json_encode($news);
        $this->redis->zAdd($cacheKey, $count, $val);
    }

    public function getNews($newsId)
    {
        $cacheKey = "item:news_$newsId";
        $news = $this->redis->get($cacheKey);
        return $news;
    }

    public function setNews($newsId, $item)
    {
        $cacheKey = "item:news_$newsId";
        $this->redis->set($cacheKey, json_encode($item));
    }

    public function getViewsData()
    {
        $key = 'news:top';
        $newsIds = $this->redis->zRange($key, 0, -1);
        if (empty($newsIds)) {
            return [];
        }
        $viewsCounts = $this->redis->zMscore($key, ...$newsIds);

        $viewsData = [];
        foreach ($newsIds as $ind => $newsId) {
            $viewsData[] = ['id' => $newsId, 'views' => $viewsCounts[$ind]];
        }
        return $viewsData;
    }
}