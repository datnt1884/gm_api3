<?php

declare(strict_types=1);

namespace App\Controller\VodCat;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetAll extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $VodCats = $this->getVodCatService()->getAll();

        return $this->jsonResponse($response, 'success', $VodCats, 200);
    }
}
