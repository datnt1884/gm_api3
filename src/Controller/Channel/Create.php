<?php

declare(strict_types=1);

namespace App\Controller\Channel;

use Slim\Http\Request;
use Slim\Http\Response;

final class Create extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $input = $request->getParsedBody();
        $Channel = $this->getChannelService()->create($input);

        return $this->jsonResponse($response, 'success', $Channel, 201);
    }
}
