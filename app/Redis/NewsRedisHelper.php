<?php

namespace App\Redis;

use Redis;

class NewsRedisHelper
{
    public function __construct(
        protected Redis $redis
    ) {}

    
}