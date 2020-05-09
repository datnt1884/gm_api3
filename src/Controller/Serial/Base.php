<?php

declare(strict_types=1);

namespace App\Controller\Serial;

use App\Controller\BaseController;
use App\Service\Serial\SerialService;
use Slim\Container;

abstract class Base extends BaseController
{
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function getSerialService(): SerialService
    {
        return $this->container->get('serial_service');
    }
}
