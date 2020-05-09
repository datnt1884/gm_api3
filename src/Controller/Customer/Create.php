<?php

declare(strict_types=1);

namespace App\Controller\Customer;

use Slim\Http\Request;
use Slim\Http\Response;

final class Create extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $input = $request->getParsedBody();
        $Customer = $this->getCustomerService()->create($input);
        $input = [
            "username" => $input['username'],
            "password" => $input['password'],
            "customer_id" => $Customer->id
        ];
        $LoginData = $this->getLoginDataService()->create($input);
        return $this->jsonResponse($response, 'success', $LoginData, 201);
    }
}
