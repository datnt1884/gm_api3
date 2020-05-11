<?php

declare(strict_types=1);

namespace App\Service\Customer;

use App\Exception\Customer;
use App\Repository\CustomerRepository;
use App\Service\BaseService;
use App\Service\RedisService;
use Respect\Validation\Validator as v;

abstract class Base extends BaseService
{
    private const REDIS_KEY = 'Customer:%s';

    protected  $CustomerRepository;

    protected  $redisService;

    public function __construct(CustomerRepository $CustomerRepository, RedisService $redisService)
    {
        $this->CustomerRepository = $CustomerRepository;
        $this->redisService = $redisService;
    }

    protected static function validateCustomerName(string $name): string
    {
        if (! v::alnum()->length(2, 100)->validate($name)) {
            throw new Customer('Invalid name.', 400);
        }

        return $name;
    }
    protected static function validateEmail(string $emailValue): string
    {
        $email = filter_var($emailValue, FILTER_SANITIZE_EMAIL);
        if (! v::email()->validate($email)) {
            throw new User('Invalid email', 400);
        }

        return $email;
    }



    protected function getCustomerFromCache(int $CustomerId)
    {
        $redisKey = sprintf(self::REDIS_KEY, $CustomerId);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $data = $this->redisService->get($key);
            $Customer = json_decode(json_encode($data), false);
        } else {
            $Customer = $this->getCustomerFromDb($CustomerId);
            $this->redisService->setex($key, $Customer);
        }

        return $Customer;
    }

    protected function getCustomerFromDb(int $CustomerId)
    {
        return $this->CustomerRepository->getCustomer($CustomerId);
    }

    protected function saveInCache($id, $Customer): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $id);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->setex($key, $Customer);
    }

    protected function deleteFromCache($CustomerId): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $CustomerId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->del($key);
    }
}
