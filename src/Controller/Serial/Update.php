<?php

declare(strict_types=1);

namespace App\Controller\Serial;

use Slim\Http\Request;
use Slim\Http\Response;

final class Update extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $input = $request->getParsedBody();
        $Serial = $this->getSerialService()->update($input, (int) $args['id']);

        return $this->jsonResponse($response, 'success', $Serial, 200);
    }
}
