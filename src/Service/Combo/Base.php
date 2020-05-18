<?php

declare(strict_types=1);

namespace App\Service\Combo;

use App\Exception\Combo;
use App\Repository\ComboRepository;
use App\Service\BaseService;
use App\Service\RedisService;
use Respect\Validation\Validator as v;

abstract class Base extends BaseService
{
    private const REDIS_KEY = 'Combo:%s';

    protected  $ComboRepository;

    protected  $redisService;

    public function __construct(ComboRepository $ComboRepository, RedisService $redisService)
    {
        $this->ComboRepository = $ComboRepository;
        $this->redisService = $redisService;
    }

    protected static function validateComboName(string $name): string
    {
        if (! v::length(2, 50)->validate($name)) {
            throw new Combo('The name of the Combo is invalid.', 400);
        }

        return $name;
    }

    protected function getOneFromCache(int $ComboId)
    {
        $redisKey = sprintf(self::REDIS_KEY, $ComboId);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $Combo = $this->redisService->get($key);
        } else {
            $Combo = $this->getOneFromDb($ComboId);
            $this->redisService->setex($key, $Combo);
        }

        return $Combo;
    }

    protected function getOneFromDb(int $ComboId)
    {
        return $this->ComboRepository->checkAndGetCombo($ComboId);
    }

    protected function saveInCache($id, $Combo): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $id);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->setex($key, $Combo);
    }

    protected function deleteFromCache($ComboId): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $ComboId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->del($key);
    }
}
