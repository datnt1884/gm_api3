<?php

declare(strict_types=1);

namespace App\Controller\Device;

use App\Controller\BaseController;
use App\Exception\Device;
use App\Service\Device\DeviceService;
use Slim\Container;

abstract class Base extends BaseController
{
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function getDeviceService(): DeviceService
    {
        return $this->container->get('device_service');
    }

    protected function checkDevicePermissions(int $DeviceId, int $DeviceIdLogged): void
    {
        if ($DeviceId !== $DeviceIdLogged) {
            throw new Device('Device permission failed.', 400);
        }
    }
}
