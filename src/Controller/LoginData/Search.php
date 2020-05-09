<?php

declare(strict_types=1);

namespace App\Controller\LoginData;

use Slim\Http\Request;
use Slim\Http\Response;

final class Search extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $LoginDatas = $this->getLoginDataService()->search($args['query']);

        return $this->jsonResponse($response, 'success', $LoginDatas, 200);
    }
}
