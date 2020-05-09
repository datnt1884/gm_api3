<?php

declare(strict_types=1);

namespace App\Service\LoginData;

use App\Exception\LoginData;
use App\Repository\LoginDataRepository;
use App\Service\BaseService;
use App\Service\RedisService;
use Respect\Validation\Validator as v;

abstract class Base extends BaseService
{
    private const REDIS_KEY = 'LoginData:%s';

    protected  $LoginDataRepository;

    protected  $redisService;

    public function __construct(LoginDataRepository $LoginDataRepository, RedisService $redisService)
    {
        $this->LoginDataRepository = $LoginDataRepository;
        $this->redisService = $redisService;
    }

    protected static function validateLoginDataName(string $name): string
    {
        if (! v::alnum()->length(2, 100)->validate($name)) {
            throw new LoginData('Invalid name.', 400);
        }

        return $name;
    }



    protected function getLoginDataFromCache(int $LoginDataId)
    {
        $redisKey = sprintf(self::REDIS_KEY, $LoginDataId);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $data = $this->redisService->get($key);
            $LoginData = json_decode(json_encode($data), false);
        } else {
            $LoginData = $this->getLoginDataFromDb($LoginDataId);
            $this->redisService->setex($key, $LoginData);
        }

        return $LoginData;
    }

    protected function getLoginDataFromDb(int $LoginDataId)
    {
        return $this->LoginDataRepository->getLoginData($LoginDataId);
    }

    protected function saveInCache($id, $LoginData): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $id);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->setex($key, $LoginData);
    }

    protected function deleteFromCache($LoginDataId): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $LoginDataId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->del($key);
    }
}
