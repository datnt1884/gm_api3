<?php

declare(strict_types=1);

namespace App\Controller\Channel;

use Slim\Http\Request;
use Slim\Http\Response;

final class Delete extends Base
{
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $input = $request->getParsedBody();
        $ChannelId = (int) $args['id'];
        $userId = (int) $input['decoded']->sub;
        $Channel = $this->getChannelService()->delete($ChannelId, $userId);

        return $this->jsonResponse($response, 'success', $Channel, 204);
    }
}
