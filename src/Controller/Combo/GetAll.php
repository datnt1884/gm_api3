<?php

declare(strict_types=1);

namespace App\Controller\Combo;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetAll extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $Combos = $this->getComboService()->getAll();

        return $this->jsonResponse($response, 'success', $Combos, 200);
    }
}
