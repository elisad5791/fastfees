<?php

namespace App\Redis;

use Ehann\RedisRaw\PhpRedisAdapter;
use Ehann\RediSearch\Index;

class SearchRedisHelper
{
    public function __construct(
        protected PhpRedisAdapter $redisAdapter
    ) {}

    public function createIndex(): void
    {
        $newsIndex = new Index($this->redisAdapter, 'news_search');

        $newsIndex->addTextField('title')->addTextField('content')->addTagField('newsid');

        $allIndices = $this->redisAdapter->rawCommand('FT._LIST', []); 

        if (!in_array('news_search', $allIndices)) {
            $newsIndex->create();
        }
    }

    public function addNews(array $item): void
    {
        $newsIndex = new Index($this->redisAdapter, 'news_search');
        $newsIndex->addTextField('title')->addTextField('content')->addTagField('newsid');

        $count = $newsIndex->tagFilter('newsid', [$item['id']])->count(); 
        $keyExists = $count > 0;
        if ($keyExists) {
            return;
        }
        
        $newsIndex->add([
            'title' => $item['title'],
            'content' => $item['content'],
            'newsid' => $item['id'],
        ]);
    }
}
