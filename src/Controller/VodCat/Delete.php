<?php

declare(strict_types=1);

namespace App\Controller\VodCat;

use Slim\Http\Request;
use Slim\Http\Response;

final class Delete extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $input = $request->getParsedBody();
        $VodCatIdLogged = $input['decoded']->sub;
        $this->checkVodCatPermissions((int) $args['id'], (int) $VodCatIdLogged);
        $VodCat = $this->getVodCatService()->delete((int) $args['id']);

        return $this->jsonResponse($response, 'success', $VodCat, 204);
    }
}
