<?php

declare(strict_types=1);

namespace App\Controller\Subscri;

use App\Controller\BaseController;
use App\Service\Subscri\SubscriService;
use App\Service\Combo\ComboService;
use App\Service\ComboPackage\ComboPackageService;


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
    protected function getComboService(): ComboService
    {
        return $this->container->get('combo_service');
    }
    protected function getComboPackageService(): ComboPackageService
    {
        return $this->container->get('comboPackage_service');
    }
}
