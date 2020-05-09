<?php

declare(strict_types=1);

namespace App\Controller\LoginData;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetOne extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $LoginData = $this->getLoginDataService()->getOne((int) $args['id']);

        return $this->jsonResponse($response, 'success', $LoginData, 200);
    }
}
