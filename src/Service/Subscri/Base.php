<?php

declare(strict_types=1);

namespace App\Service\Subscri;

use App\Exception\Subscri;
use App\Repository\SubscriRepository;
use App\Service\BaseService;
use App\Service\RedisService;
use Respect\Validation\Validator as v;

abstract class Base extends BaseService
{
    private const REDIS_KEY = 'Subscri:%s:user:%s';

    protected  $SubscriRepository;

    protected  $redisService;

    public function __construct(SubscriRepository $SubscriRepository, RedisService $redisService)
    {
        $this->SubscriRepository = $SubscriRepository;
        $this->redisService = $redisService;
    }

    protected function getSubscriRepository(): SubscriRepository
    {
        return $this->SubscriRepository;
    }
    public function getDeviceid(int $userId): array
    {
        return $this->getSubscriRepository()->getDeviceid($userId);
    }

    protected static function validateSubscriName(string $name): string
    {
        if (! v::length(2, 100)->validate($name)) {
            throw new Subscri('Invalid name.', 400);
        }

        return $name;
    }

    protected static function validateSubscriStatus(int $status): int
    {
        if (! v::numeric()->between(0, 1)->validate($status)) {
            throw new Subscri('Invalid status', 400);
        }

        return $status;
    }

    protected function getSubscriFromCache(int $SubscriId, int $userId)
    {
        $redisKey = sprintf(self::REDIS_KEY, $SubscriId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $Subscri = $this->redisService->get($key);
        } else {
            $Subscri = $this->getSubscriFromDb($SubscriId, $userId);
            $this->redisService->setex($key, $Subscri);
        }

        return $Subscri;
    }

    protected function getSubscriFromDb(int $SubscriId, int $userId)
    {
        return $this->getSubscriRepository()->checkAndGetSubscri($SubscriId, $userId);
    }

    protected function saveInCache($SubscriId, $userId, $Subscris): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $SubscriId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->setex($key, $Subscris);
    }

    protected function deleteFromCache($SubscriId, $userId): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $SubscriId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->del($key);
    }
}
