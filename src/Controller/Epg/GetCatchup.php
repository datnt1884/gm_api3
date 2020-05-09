<?php

declare(strict_types=1);

namespace App\Controller\Epg;

use Slim\Http\Request;
use Slim\Http\Response;

final class GetCatchup extends Base
{
    public function __invoke(Request $request, Response $response,array $args): Response
    {
        // $input = $request->getParsedBody();
      //  $ChannelId = (int) $args['channel_id'];

        $Epgs = $this->GetEpgService()->GetCatchup($args);
        for ($i=0;$i<count($Epgs);$i++)
        {
            $epotime = strtotime($Epgs[$i]['program_start']);
            //    echo $epotime;
            // exit;
            $Epgs[$i]['channel.stream_url'] = str_replace('[epochtime]', $epotime, $Epgs[$i]['channel.stream_url']);
        }

        return $this->jsonResponse($response, 'success', $Epgs, 200);
    }
}
