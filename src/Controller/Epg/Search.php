<?php

declare(strict_types=1);

namespace App\Controller\Epg;

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
        $Epgs = $this->getEpgService()->search($query, $userId, $status);

        return $this->jsonResponse($response, 'success', $Epgs, 200);
    }
}
