<?php

declare(strict_types=1);

namespace App\Service\Task;

use App\Exception\Task;
use App\Repository\TaskRepository;
use App\Service\BaseService;
use App\Service\RedisService;
use Respect\Validation\Validator as v;

abstract class Base extends BaseService
{
    private const REDIS_KEY = 'task:%s:user:%s';

    protected  $taskRepository;

    protected  $redisService;

    public function __construct(TaskRepository $taskRepository, RedisService $redisService)
    {
        $this->taskRepository = $taskRepository;
        $this->redisService = $redisService;
    }

    protected function getTaskRepository(): TaskRepository
    {
        return $this->taskRepository;
    }

    protected static function validateTaskName(string $name): string
    {
        if (! v::length(2, 100)->validate($name)) {
            throw new Task('Invalid name.', 400);
        }

        return $name;
    }

    protected static function validateTaskStatus(int $status): int
    {
        if (! v::numeric()->between(0, 1)->validate($status)) {
            throw new Task('Invalid status', 400);
        }

        return $status;
    }

    protected function getTaskFromCache(int $taskId, int $userId)
    {
        $redisKey = sprintf(self::REDIS_KEY, $taskId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        if ($this->redisService->exists($key)) {
            $task = $this->redisService->get($key);
        } else {
            $task = $this->getTaskFromDb($taskId, $userId);
            $this->redisService->setex($key, $task);
        }

        return $task;
    }

    protected function getTaskFromDb(int $taskId, int $userId)
    {
        return $this->getTaskRepository()->checkAndGetTask($taskId, $userId);
    }

    protected function saveInCache($taskId, $userId, $tasks): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $taskId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->setex($key, $tasks);
    }

    protected function deleteFromCache($taskId, $userId): void
    {
        $redisKey = sprintf(self::REDIS_KEY, $taskId, $userId);
        $key = $this->redisService->generateKey($redisKey);
        $this->redisService->del($key);
    }
}
