<?php

declare(strict_types=1);

namespace App\Controller;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

final class DefaultController extends BaseController
{
    public const API_VERSION = '0.55.0';

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    public function getDateEpg (Request $request, Response $response): Response
    {
        $day = date('m-d');

        for ( $i = 0; $i <= 13; $i++ ) {

            $List[]  = date( 'm-d', strtotime( "+$i day"));
        }

        return $this->jsonResponse($response, 'success', $List, 200);

    }
    public function getDateCatchup (Request $request, Response $response): Response
    {
        $day = date('m-d');

        for ( $i = 0; $i <= 13; $i++ ) {

            $List[]  = date( 'm-d', strtotime( "-$i day"));
        }

        return $this->jsonResponse($response, 'success', $List, 200);
    }
    public function getHelp(Request $request, Response $response): Response
    {
        $url = getenv('APP_DOMAIN');
        $endpoints = [
            'tasks' => $url . '/api/v1/tasks',
            'users' => $url . '/api/v1/users',
            'notes' => $url . '/api/v1/notes',
            'docs' => $url . '/docs/index.html',
            'status' => $url . '/status',
            'this help' => $url . '',
        ];
        $message = [
            'endpoints' => $endpoints,
            'version' => self::API_VERSION,
            'timestamp' => time(),
        ];

        return $this->jsonResponse($response, 'success', $message, 200);
    }

    public function getStatus(Request $request, Response $response): Response
    {
        $status = [
            'stats' => $this->getDbStats(),
            'MySQL' => 'OK',
            'Redis' => $this->checkRedisConnection(),
            'version' => self::API_VERSION,
            'timestamp' => time(),
        ];

        return $this->jsonResponse($response, 'success', $status, 200);
    }



    private function getDbStats(): array
    {
        $userService = $this->container->get('user_service');
        $taskService = $this->container->get('task_service');
        $noteService = $this->container->get('note_service');

        return [
            'users' => count($userService->getAll()),
            'tasks' => count($taskService->getAllTasks()),
            'notes' => count($noteService->getAll()),
        ];
    }

    private function checkRedisConnection(): string
    {
        $redis = 'Disabled';
        if (self::isRedisEnabled() === true) {
            $redisService = $this->container->get('redis_service');
            $redisKey = 'test:status';
            $key = $redisService->generateKey($redisKey);
            $redisService->set($key, []);
            $redis = 'OK';
        }

        return $redis;
    }
}
