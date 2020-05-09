<?php

declare(strict_types=1);

namespace App\Service\Epg;

use App\Exception\Epg;
use App\Repository\EpgRepository;
use App\Service\BaseService;
use App\Service\RedisService;
use Respect\Validation\Validator as v;

abstract class Base extends BaseService
{
    private const REDIS_KEY = 'Epg:%s:user:%s';

    protected  $EpgRepository;

    protected  $redisService;

    public function __construct(EpgRepository $EpgRepository, RedisService $redisService)
    {
        $this->EpgRepository = $EpgRepository;
        $this->redisService = $redisService;
    }

    protected function getEpgRepository(): EpgRepository
    {
        return $this->EpgRepository;
    }
    public function getDeviceid(int $userId): array
    {
        return $this->getEpgRepository()->getDeviceid($userId);
    }
    protected static function validateEpgName(string $name): string
    {
        if (! v::length(2, 100)->validate($name)) {
            throw new Epg('Invalid name.', 400);
        }

        return $name;
    }

    protected static function validateEpgStatus(int $status): int
    {
        if (! v::numeric()->between(0, 1)->validate($status)) {
            throw new Epg('Invalid status', 400);
        }

        return $status;
    }

    protected function getEpgFromCache(int $EpgId, int $userId)
    {
        $redisKey = sprintf(self::REDIS_KEY, $EpgId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $Epg = $this->redisService->get($key);
        } else {
            $Epg = $this->getEpgFromDb($EpgId, $userId);
            $this->redisService->setex($key, $Epg);
        }

        return $Epg;
    }

    protected function getEpgFromDb(int $EpgId, int $userId)
    {
        return $this->getEpgRepository()->checkAndGetEpg($EpgId, $userId);
    }

    protected function saveInCache($EpgId, $userId, $Epgs): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $EpgId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->setex($key, $Epgs);
    }

    protected function deleteFromCache($EpgId, $userId): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $EpgId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->del($key);
    }
}
