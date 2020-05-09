<?php

declare(strict_types=1);

namespace App\Controller\Update;

use Slim\Http\Request;
use Slim\Http\Response;

final class Create extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $input = $request->getParsedBody();
        $Update = $this->getUpdateService()->create($input);

        return $this->jsonResponse($response, 'success', $Update, 201);
    }
}
