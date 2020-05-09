<?php

declare(strict_types=1);

namespace App\Controller\Customer;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetAll extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $Customers = $this->getCustomerService()->getAll();

        return $this->jsonResponse($response, 'success', $Customers, 200);
    }
}
