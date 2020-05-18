<?php

declare(strict_types=1);

namespace App\Controller\Subscri;

use App\Exception\Subscri;
use Slim\Http\Request;
use Slim\Http\Response;

final class Create extends Base
{
    public function __invoke(Request $request, Response $response): Response
    {
        $input = $request->getParsedBody();
        $userId = (int) $input['decoded']->sub;
        $deviceid = $this->getSubscriService()->getDeviceid($userId);
        $login_id = $deviceid[0]['login_data_id'];
        $username = $deviceid[0]['username'];
        $Combo = $this->getComboService()->getOne((int)$input['combo_id']);
        $ComboPackage = $this->getComboPackageService()->getByComboId((int)$input['combo_id']);
        $start_date = date('Y-m-d')." 00:00:00";
       // var_dump($ComboPackage);
      //  echo $ComboPackage[0]['package_id'];
        $Subcris = $this->getSubscriService()->getAll((int) $deviceid[0]['login_data_id']);

        foreach ($ComboPackage as $c)
        {
            $package_id = $c['package_id'];
            $end_date = date('Y-m-d H:i:s',strtotime($start_date. "+$Combo->duration day"));
            $input = [
                    "company_id"=>1,
                    "login_id"=> $login_id,
                    "customer_username"=>$username,
                    "user_username"=>"admin",
                    "start_date"=>$start_date,
                    "end_date"=>$end_date,
                    "package_id"=>$package_id
            ];
            //$input = json_decode(json_encode($input), false);
            if (empty($Subcris))
            {
                $Subscri = $this->getSubscriService()->create($input);
            }
            else $Subscri = $this->getSubscriService()->update($input);

           // echo $end_date . "\n";
        }

        return $this->jsonResponse($response, 'success', $Subscri, 201);
    }
}
