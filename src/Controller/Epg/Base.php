<?php

declare(strict_types=1);

namespace App\Controller\Epg;

use App\Controller\BaseController;
use App\Service\Epg\EpgService;
use Slim\Container;

abstract class Base extends BaseController
{
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function getEpgService(): EpgService
    {
        return $this->container->get('epg_service');
    }
}
