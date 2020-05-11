<?php

declare(strict_types=1);

namespace App\Controller\Vod;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetByCat extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $input = $request->getParsedBody();
       // var_dump($args);

        $userId = (int) $input['decoded']->sub;
        $deviceid = $this->getVodService()->getDeviceid($userId);
        $args['login_data_id'] =  $deviceid[0]['login_data_id'];
       // echo $args['category_id'];
        $Vod = $this->getVodService()->getByCat($args);
        return $this->jsonResponse($response, 'success', $Vod, 200);
    }
}
