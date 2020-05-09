<?php

declare(strict_types=1);

namespace App\Controller\VodCat;

use App\Controller\BaseController;
use App\Exception\VodCat;
use App\Service\VodCat\VodCatService;
use Slim\Container;

abstract class Base extends BaseController
{
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function getVodCatService(): VodCatService
    {
        return $this->container->get('vodCat_service');
    }


}
