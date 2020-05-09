<?php

declare(strict_types=1);

namespace App\Controller\Vod;

use App\Controller\BaseController;
use App\Service\Vod\VodService;
use Slim\Container;

abstract class Base extends BaseController
{
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function getVodService(): VodService
    {
        return $this->container->get('vod_service');
    }
}
