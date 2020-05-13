<?php

declare(strict_types=1);

namespace App\Controller\Subscri;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetAll extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $input = $request->getParsedBody();
        $userId = (int) $input['decoded']->sub;
        $deviceid = $this->getSubscriService()->getDeviceid($userId);

        $Subcris = $this->getSubscriService()->getAll((int) $deviceid[0]['login_data_id']);

        return $this->jsonResponse($response, 'success', $Subcris, 200);
    }
}
