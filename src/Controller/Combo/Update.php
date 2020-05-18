<?php

declare(strict_types=1);

namespace App\Controller\Combo;

use Slim\Http\Request;
use Slim\Http\Response;

final class Update extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $input = $request->getParsedBody();
        $Combo = $this->getComboService()->update($input, (int) $args['id']);

        return $this->jsonResponse($response, 'success', $Combo, 200);
    }
}
