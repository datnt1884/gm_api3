<?php

declare(strict_types=1);

namespace App\Controller\Serial;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetOne extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $input = $request->getParsedBody();
        $SerialId = (int) $args['id'];
        $userId = (int) $input['decoded']->sub;
        $Serial = $this->getSerialService()->getOne($SerialId, $userId);

        return $this->jsonResponse($response, 'success', $Serial, 200);
    }
}
