<?php

declare(strict_types=1);

namespace App\Service\VodCat;

use App\Exception\VodCat;
use App\Repository\VodCatRepository;
use App\Service\BaseService;
use App\Service\RedisService;
use Respect\Validation\Validator as v;

abstract class Base extends BaseService
{
    private const REDIS_KEY = 'VodCat:%s';

    protected  $VodCatRepository;

    protected  $redisService;

    public function __construct(VodCatRepository $VodCatRepository, RedisService $redisService)
    {
        $this->VodCatRepository = $VodCatRepository;
        $this->redisService = $redisService;
    }

    protected static function validateVodCatName(string $name): string
    {
        if (! v::alnum()->length(2, 100)->validate($name)) {
            throw new VodCat('Invalid name.', 400);
        }

        return $name;
    }

    protected static function validateEmail(string $emailValue): string
    {
        $email = filter_var($emailValue, FILTER_SANITIZE_EMAIL);
        if (! v::email()->validate($email)) {
            throw new VodCat('Invalid email', 400);
        }

        return $email;
    }

    protected function getVodCatFromCache(int $VodCatId)
    {
        $redisKey = sprintf(self::REDIS_KEY, $VodCatId);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $data = $this->redisService->get($key);
            $VodCat = json_decode(json_encode($data), false);
        } else {
            $VodCat = $this->getVodCatFromDb($VodCatId);
            $this->redisService->setex($key, $VodCat);
        }

        return $VodCat;
    }

    protected function getVodCatFromDb(int $VodCatId)
    {
        return $this->VodCatRepository->getVodCat($VodCatId);
    }

    protected function saveInCache($id, $VodCat): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $id);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->setex($key, $VodCat);
    }

    protected function deleteFromCache($VodCatId): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $VodCatId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->del($key);
    }
}
