<?php

declare(strict_types=1);

namespace App\Controller\Serial;

use Slim\Http\Request;
use Slim\Http\Response;

final class Search extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $input = $request->getParsedBody();
        $userId = (int) $input['decoded']->sub;
        $query = '';
        if (isset($args['query'])) {
            $query = $args['query'];
        }
        $status = $request->getParam('status', null);
        $Serials = $this->getSerialService()->search($query, $userId, $status);

        return $this->jsonResponse($response, 'success', $Serials, 200);
    }
}
