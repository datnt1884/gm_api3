<?php

declare(strict_types=1);

namespace App\Controller\Vod;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetByCat extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $input = $request->getParsedBody();

        $userId = (int) $input['decoded']->sub;
        $deviceid = $this->getVodService()->getDeviceid($userId);
        $args['login_data_id'] = $deviceid[0]['login_data_id'];

        $Vod = $this->getVodService()->getbyCat($args);
        return $this->jsonResponse($response, 'success', $Vod, 200);
    }
}
