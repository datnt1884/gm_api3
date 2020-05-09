<?php

declare(strict_types=1);

namespace App\Service\Device;

use App\Exception\Device;
use App\Repository\DeviceRepository;
use App\Service\BaseService;
use App\Service\RedisService;
use Respect\Validation\Validator as v;

abstract class Base extends BaseService
{
    private const REDIS_KEY = 'Device:%s';

    protected  $DeviceRepository;

    protected  $redisService;

    public function __construct(DeviceRepository $DeviceRepository, RedisService $redisService)
    {
        $this->DeviceRepository = $DeviceRepository;
        $this->redisService = $redisService;
    }

    protected static function validateDeviceName(string $name): string
    {
        if (! v::alnum()->length(2, 100)->validate($name)) {
            throw new Device('Invalid name.', 400);
        }

        return $name;
    }



    protected function getDeviceFromCache(int $DeviceId)
    {
        $redisKey = sprintf(self::REDIS_KEY, $DeviceId);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $data = $this->redisService->get($key);
            $Device = json_decode(json_encode($data), false);
        } else {
            $Device = $this->getDeviceFromDb($DeviceId);
            $this->redisService->setex($key, $Device);
        }

        return $Device;
    }

    protected function getDeviceFromDb(int $DeviceId)
    {
        return $this->DeviceRepository->getDevice($DeviceId);
    }

    protected function saveInCache($id, $Device): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $id);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->setex($key, $Device);
    }

    protected function deleteFromCache($DeviceId): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $DeviceId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->del($key);
    }
}
