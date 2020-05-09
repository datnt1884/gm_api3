<?php

declare(strict_types=1);

namespace App\Controller\Update;

use App\Controller\BaseController;
use App\Service\Update\UpdateService;
use Slim\Container;

abstract class Base extends BaseController
{
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function getUpdateService(): UpdateService
    {
        return $this->container->get('Update_service');
    }
}
