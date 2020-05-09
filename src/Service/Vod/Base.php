<?php

declare(strict_types=1);

namespace App\Service\Vod;

use App\Exception\Vod;
use App\Repository\VodRepository;
use App\Service\BaseService;
use App\Service\RedisService;
use Respect\Validation\Validator as v;

abstract class Base extends BaseService
{
    private const REDIS_KEY = 'Vod:%s:user:%s';

    protected  $VodRepository;

    protected  $redisService;

    public function __construct(VodRepository $VodRepository, RedisService $redisService)
    {
        $this->VodRepository = $VodRepository;
        $this->redisService = $redisService;
    }

    protected function getVodRepository(): VodRepository
    {
        return $this->VodRepository;
    }
    public function getDeviceid(int $userId): array
    {
        return $this->getVodRepository()->getDeviceid($userId);
    }
    protected static function validateVodName(string $name): string
    {
        if (! v::length(2, 100)->validate($name)) {
            throw new Vod('Invalid name.', 400);
        }

        return $name;
    }

    protected static function validateVodStatus(int $status): int
    {
        if (! v::numeric()->between(0, 1)->validate($status)) {
            throw new Vod('Invalid status', 400);
        }

        return $status;
    }

    protected function getVodFromCache(int $VodId, int $userId)
    {
        $redisKey = sprintf(self::REDIS_KEY, $VodId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $Vod = $this->redisService->get($key);
        } else {
            $Vod = $this->getVodFromDb($VodId, $userId);
            $this->redisService->setex($key, $Vod);
        }

        return $Vod;
    }

    protected function getVodFromDb(int $VodId, int $userId)
    {
        return $this->getVodRepository()->checkAndGetVod($VodId, $userId);
    }

    protected function saveInCache($VodId, $userId, $Vods): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $VodId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->setex($key, $Vods);
    }

    protected function deleteFromCache($VodId, $userId): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $VodId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->del($key);
    }
}
