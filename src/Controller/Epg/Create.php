<?php

declare(strict_types=1);

namespace App\Controller\Epg;

use Slim\Http\Request;
use Slim\Http\Response;

final class Create extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $input = $request->getParsedBody();
        $Epg = $this->getEpgService()->create($input);

        return $this->jsonResponse($response, 'success', $Epg, 201);
    }
}
