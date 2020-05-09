<?php

declare(strict_types=1);

namespace App\Controller\Device;

use Slim\Http\Request;
use Slim\Http\Response;

final class Delete extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $input = $request->getParsedBody();
        $DeviceIdLogged = $input['decoded']->sub;
        $this->checkDevicePermissions((int) $args['id'], (int) $DeviceIdLogged);
        $Device = $this->getDeviceService()->delete((int) $args['id']);

        return $this->jsonResponse($response, 'success', $Device, 204);
    }
}
