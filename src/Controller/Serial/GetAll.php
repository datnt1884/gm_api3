<?php

declare(strict_types=1);

namespace App\Controller\Serial;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetAll extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $input = $request->getParsedBody();
        $userId = (int) $input['decoded']->sub;
        $Serials = $this->getSerialService()->getAll($userId);

        return $this->jsonResponse($response, 'success', $Serials, 200);
    }
}
