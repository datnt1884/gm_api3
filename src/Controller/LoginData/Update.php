<?php

declare(strict_types=1);

namespace App\Controller\LoginData;

use Slim\Http\Request;
use Slim\Http\Response;

final class Update extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $input = $request->getParsedBody();
        $LoginDataIdLogged = $input['decoded']->sub;
        $this->checkLoginDataPermissions((int) $args['id'], (int) $LoginDataIdLogged);
        $LoginData = $this->getLoginDataService()->update($input, (int) $args['id']);

        return $this->jsonResponse($response, 'success', $LoginData, 200);
    }
}
