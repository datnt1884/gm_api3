<?php

declare(strict_types=1);

namespace App\Controller\Vod;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetOne extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $input = $request->getParsedBody();
        $VodId = (int) $args['id'];
        $userId = (int) $input['decoded']->sub;
        $Vod = $this->getVodService()->getOne($VodId, $userId);

        return $this->jsonResponse($response, 'success', $Vod, 200);
    }
}
