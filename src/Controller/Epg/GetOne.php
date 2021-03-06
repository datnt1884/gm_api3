<?php

declare(strict_types=1);

namespace App\Controller\Epg;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetOne extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $input = $request->getParsedBody();
        $EpgId = (int) $args['id'];
        $userId = (int) $input['decoded']->sub;
        $Epg = $this->getEpgService()->getOne($EpgId, $userId);

        return $this->jsonResponse($response, 'success', $Epg, 200);
    }
}
