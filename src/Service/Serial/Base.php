<?php

declare(strict_types=1);

namespace App\Service\Serial;

use App\Exception\Serial;
use App\Repository\SerialRepository;
use App\Service\BaseService;
use App\Service\RedisService;
use Respect\Validation\Validator as v;

abstract class Base extends BaseService
{
    private const REDIS_KEY = 'Serial:%s:user:%s';

    protected  $SerialRepository;

    protected  $redisService;

    public function __construct(SerialRepository $SerialRepository, RedisService $redisService)
    {
        $this->SerialRepository = $SerialRepository;
        $this->redisService = $redisService;
    }

    protected function getSerialRepository(): SerialRepository
    {
        return $this->SerialRepository;
    }

    protected static function validateSerialName(string $name): string
    {
        if (! v::length(2, 100)->validate($name)) {
            throw new Serial('Invalid name.', 400);
        }

        return $name;
    }

    protected static function validateSerialStatus(int $status): int
    {
        if (! v::numeric()->between(0, 1)->validate($status)) {
            throw new Serial('Invalid status', 400);
        }

        return $status;
    }

    protected function getSerialFromCache(int $SerialId, int $userId)
    {
        $redisKey = sprintf(self::REDIS_KEY, $SerialId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $Serial = $this->redisService->get($key);
        } else {
            $Serial = $this->getSerialFromDb($SerialId, $userId);
            $this->redisService->setex($key, $Serial);
        }

        return $Serial;
    }

    protected function getSerialFromDb(int $SerialId, int $userId)
    {
        return $this->getSerialRepository()->checkAndGetSerial($SerialId, $userId);
    }

    protected function saveInCache($SerialId, $userId, $Serials): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $SerialId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->setex($key, $Serials);
    }

    protected function deleteFromCache($SerialId, $userId): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $SerialId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->del($key);
    }
}
