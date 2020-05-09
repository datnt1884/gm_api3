<?php

declare(strict_types=1);

namespace App\Service;
use App\Repository\BaseRepository;

abstract class BaseService
{

    protected static function isRedisEnabled(): bool
    {
        return filter_var(getenv('REDIS_ENABLED'), FILTER_VALIDATE_BOOLEAN);
    }

}
