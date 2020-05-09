<?php

declare(strict_types=1);

namespace App\Controller\LoginData;

use App\Controller\BaseController;
use App\Exception\LoginData;
use App\Service\LoginData\LoginDataService;
use Slim\Container;

abstract class Base extends BaseController
{
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function getLoginDataService(): LoginDataService
    {
        return $this->container->get('loginData_service');
    }

    protected function checkLoginDataPermissions(int $LoginDataId, int $LoginDataIdLogged): void
    {
        if ($LoginDataId !== $LoginDataIdLogged) {
            throw new LoginData('LoginData permission failed.', 400);
        }
    }
}
