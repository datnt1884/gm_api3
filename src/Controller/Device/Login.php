<?php

declare(strict_types=1);

namespace App\Controller\Device;

use Slim\Http\Request;
use Slim\Http\Response;

final class Login extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $input = $request->getParsedBody();
        $jwt = $this->getDeviceService()->login($input);
        $message = [
            'Authorization' => 'Bearer ' . $jwt,
        ];

        return $this->jsonResponse($response, 'success', $message, 200);
    }
}
