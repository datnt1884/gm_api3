<?php

declare(strict_types=1);

namespace App\Controller\Vod;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetAll extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $input = $request->getParsedBody();
        $userId = (int) $input['decoded']->sub;
        $Vods = $this->getVodService()->getAll($userId);

        return $this->jsonResponse($response, 'success', $Vods, 200);
    }
}
