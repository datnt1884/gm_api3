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
       // echo
        $input['firstname']= substr($input['email'], 0, strpos($input['email'], '@'));
        $input['lastname']  = substr($input['email'], 0, strpos($input['email'], '@'));
        $Customer = $this->getCustomerService()->create($input);
        $input1 = [
            "username" => "u".$Customer->id."_".bin2hex(random_bytes(4)),
            "password" => bin2hex(random_bytes(4)),
            "customer_id" => $Customer->id
        ];

       $LoginData = $this->getLoginDataService()->create($input1);
        return $this->jsonResponse($response, 'success', $LoginData, 201);
    }
}
