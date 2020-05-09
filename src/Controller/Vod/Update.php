<?php

declare(strict_types=1);

namespace App\Controller\Vod;

use Slim\Http\Request;
use Slim\Http\Response;

final class Update extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $input = $request->getParsedBody();
        $Vod = $this->getVodService()->update($input, (int) $args['id']);

        return $this->jsonResponse($response, 'success', $Vod, 200);
    }
}
