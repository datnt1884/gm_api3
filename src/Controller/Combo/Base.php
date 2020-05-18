<?php

declare(strict_types=1);

namespace App\Controller\Combo;

use App\Controller\BaseController;
use App\Service\Combo\ComboService;
use Slim\Container;

abstract class Base extends BaseController
{
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function getComboService(): ComboService
    {
        return $this->container->get('combo_service');
    }
}
