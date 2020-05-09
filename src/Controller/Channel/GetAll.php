<?php

declare(strict_types=1);

namespace App\Controller\Channel;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetAll extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $input = $request->getParsedBody();
        $userId = (int) $input['decoded']->sub;
        $deviceid = $this->getChannelService()->getDeviceid($userId);
        $Channels = $this->getChannelService()->getAll((int) $deviceid[0]['login_data_id']);
        for ($i=0;$i<count($Channels);$i++)
        {
            //   $epotime = strtotime($Channels[$i]['program_start']);
            //    echo $epotime;
            // exit;
            $Channels[$i]['channel_streams.token_url'] = "http://class.ttvmax.com/gen_token.php";
            //$books[$i]['channel.stream_url'] = "http://";
        }

        return $this->jsonResponse($response, 'success', $Channels, 200);
    }
}
