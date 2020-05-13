<?php

declare(strict_types=1);

namespace App\Controller\Subscri;

use App\Controller\BaseController;
use App\Service\Subscri\SubscriService;
use Slim\Container;

abstract class Base extends BaseController
{
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function getSubscriService(): SubscriService
    {
        return $this->container->get('subscri_service');
    }
}
