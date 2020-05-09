<?php

declare(strict_types=1);

namespace App\Controller\Subscri;

use Slim\Http\Request;
use Slim\Http\Response;

final class Update extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $input = $request->getParsedBody();
        $Subscri = $this->getSubscriService()->update($input, (int) $args['id']);

        return $this->jsonResponse($response, 'success', $Subscri, 200);
    }
}
