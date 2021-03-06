<?php

declare(strict_types=1);

namespace App\Controller\Customer;

use Slim\Http\Request;
use Slim\Http\Response;

final class Update extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $input = $request->getParsedBody();
        $CustomerIdLogged = $input['decoded']->sub;
        $this->checkCustomerPermissions((int) $args['id'], (int) $CustomerIdLogged);
        $Customer = $this->getCustomerService()->update($input, (int) $args['id']);

        return $this->jsonResponse($response, 'success', $Customer, 200);
    }
}
