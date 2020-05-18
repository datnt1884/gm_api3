<?php

declare(strict_types=1);

namespace App\Service\ComboPackage;

use App\Exception\ComboPackage;
use App\Repository\ComboPackageRepository;
use App\Service\BaseService;
use App\Service\RedisService;
use Respect\Validation\Validator as v;

abstract class Base extends BaseService
{
    private const REDIS_KEY = 'ComboPackage:%s';

    protected  $ComboPackageRepository;

    protected  $redisService;

    public function __construct(ComboPackageRepository $ComboPackageRepository, RedisService $redisService)
    {
        $this->ComboPackageRepository = $ComboPackageRepository;
        $this->redisService = $redisService;
    }

    protected static function validateComboPackageName(string $name): string
    {
        if (! v::length(2, 50)->validate($name)) {
            throw new ComboPackage('The name of the ComboPackage is invalid.', 400);
        }

        return $name;
    }

    protected function getOneFromCache(int $ComboPackageId)
    {
        $redisKey = sprintf(self::REDIS_KEY, $ComboPackageId);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $ComboPackage = $this->redisService->get($key);
        } else {
            $ComboPackage = $this->getOneFromDb($ComboPackageId);
            $this->redisService->setex($key, $ComboPackage);
        }

        return $ComboPackage;
    }

    protected function getOneFromDb(int $ComboPackageId)
    {
        return $this->ComboPackageRepository->checkAndGetComboPackage($ComboPackageId);
    }

    protected function saveInCache($id, $ComboPackage): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $id);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->setex($key, $ComboPackage);
    }

    protected function deleteFromCache($ComboPackageId): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $ComboPackageId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->del($key);
    }
}
