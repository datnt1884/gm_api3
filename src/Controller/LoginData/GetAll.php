<?php

declare(strict_types=1);

namespace App\Controller\LoginData;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetAll extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $LoginDatas = $this->getLoginDataService()->getAll();

        return $this->jsonResponse($response, 'success', $LoginDatas, 200);
    }
}
