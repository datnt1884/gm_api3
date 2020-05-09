<?php

declare(strict_types=1);

namespace App\Controller\Customer;

use App\Controller\BaseController;
use App\Exception\Customer;
use App\Service\Customer\CustomerService;
use Slim\Container;

abstract class Base extends BaseController
{
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    protected function getCustomerService(): CustomerService
    {
        return $this->container->get('customer_service');
    }

    protected function checkCustomerPermissions(int $CustomerId, int $CustomerIdLogged): void
    {
        if ($CustomerId !== $CustomerIdLogged) {
            throw new Customer('Customer permission failed.', 400);
        }
    }
}
