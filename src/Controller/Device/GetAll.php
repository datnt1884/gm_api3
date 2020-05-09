<?php

declare(strict_types=1);

namespace App\Controller\Device;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetAll extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $Devices = $this->getDeviceService()->getAll();

        return $this->jsonResponse($response, 'success', $Devices, 200);
    }
}
