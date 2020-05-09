<?php

declare(strict_types=1);

namespace App\Controller\Customer;

use Slim\Http\Request;
use Slim\Http\Response;

final class Login extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $input = $request->getParsedBody();
        $LoginData = $this->getLoginDataService()->search($input['username']);
        $input['login_data_id'] = $LoginData[0]['id'];

        $device = $this->getDeviceService()->create($input);
        //    $message = [
        //      'Authorization' => 'Bearer ' . $jwt,
        //];


        return $this->jsonResponse($response, 'success', $device, 200);
    }
}
