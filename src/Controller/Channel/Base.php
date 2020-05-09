<?php

declare(strict_types=1);

namespace App\Controller\Channel;

use App\Controller\BaseController;
use App\Service\Channel\ChannelService;
use Slim\Container;

abstract class Base extends BaseController
{
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function getChannelService(): ChannelService
    {
        return $this->container->get('channel_service');
    }
}
