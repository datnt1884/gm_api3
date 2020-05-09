<?php

declare(strict_types=1);

namespace App\Controller\VodCat;

use Slim\Http\Request;
use Slim\Http\Response;

final class Create extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $input = $request->getParsedBody();
        $VodCat = $this->getVodCatService()->create($input);

        return $this->jsonResponse($response, 'success', $VodCat, 201);
    }
}
