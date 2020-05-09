<?php

declare(strict_types=1);

namespace App\Controller\Subscri;

use Slim\Http\Request;
use Slim\Http\Response;

final class Delete extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $input = $request->getParsedBody();
        $SubscriId = (int) $args['id'];
        $userId = (int) $input['decoded']->sub;
        $Subscri = $this->getSubscriService()->delete($SubscriId, $userId);

        return $this->jsonResponse($response, 'success', $Subscri, 204);
    }
}
