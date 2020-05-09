<?php

declare(strict_types=1);

namespace App\Controller\Update;

use Slim\Http\Request;
use Slim\Http\Response;

final class Delete extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $input = $request->getParsedBody();
        $UpdateId = (int) $args['id'];
        $userId = (int) $input['decoded']->sub;
        $Update = $this->getUpdateService()->delete($UpdateId, $userId);

        return $this->jsonResponse($response, 'success', $Update, 204);
    }
}
