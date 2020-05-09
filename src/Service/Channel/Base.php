<?php

declare(strict_types=1);

namespace App\Service\Channel;

use App\Exception\Channel;
use App\Repository\ChannelRepository;
use App\Service\BaseService;
use App\Service\RedisService;
use Respect\Validation\Validator as v;

abstract class Base extends BaseService
{
    private const REDIS_KEY = 'Channel:%s:user:%s';

    protected  $ChannelRepository;

    protected  $redisService;

    public function __construct(ChannelRepository $ChannelRepository, RedisService $redisService)
    {
        $this->ChannelRepository = $ChannelRepository;
        $this->redisService = $redisService;
    }

    protected function getChannelRepository(): ChannelRepository
    {
        return $this->ChannelRepository;
    }
    public function getDeviceid(int $userId): array
    {
        return $this->getChannelRepository()->getDeviceid($userId);
    }
    protected static function validateChannelName(string $name): string
    {
        if (! v::length(2, 100)->validate($name)) {
            throw new Channel('Invalid name.', 400);
        }

        return $name;
    }

    protected static function validateChannelStatus(int $status): int
    {
        if (! v::numeric()->between(0, 1)->validate($status)) {
            throw new Channel('Invalid status', 400);
        }

        return $status;
    }

    protected function getChannelFromCache(int $ChannelId, int $userId)
    {
        $redisKey = sprintf(self::REDIS_KEY, $ChannelId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $Channel = $this->redisService->get($key);
        } else {
            $Channel = $this->getChannelFromDb($ChannelId, $userId);
            $this->redisService->setex($key, $Channel);
        }

        return $Channel;
    }

    protected function getChannelFromDb(int $ChannelId, int $userId)
    {
        return $this->getChannelRepository()->checkAndGetChannel($ChannelId, $userId);
    }

    protected function saveInCache($ChannelId, $userId, $Channels): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $ChannelId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->setex($key, $Channels);
    }

    protected function deleteFromCache($ChannelId, $userId): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $ChannelId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->del($key);
    }
}
