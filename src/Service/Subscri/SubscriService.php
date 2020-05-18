<?php

declare(strict_types=1);

namespace App\Service\Subscri;

use App\Exception\Subscri;

final class SubscriService extends Base
{
    public function getAllSubscris(): array
    {
        return $this->getSubscriRepository()->getAllSubscris();
    }

    public function getAll(int $userId): array
    {
        return $this->getSubscriRepository()->getAll($userId);
    }

    public function getOne(int $SubscriId, int $userId)
    {
        if (self::isRedisEnabled() === true) {
            $Subscri = $this->getSubscriFromCache($SubscriId, $userId);
        } else {
            $Subscri = $this->getSubscriFromDb($SubscriId, $userId);
        }

        return $Subscri;
    }

    public function search(string $SubscrisName, int $userId, $status): array
    {
        if ($status !== null) {
            $status = (int) $status;
        }

        return $this->getSubscriRepository()->search($SubscrisName, $userId, $status);
    }

    public function create(array $input)
    {
        $data = json_decode(json_encode($input), false);
        //if (! isset($data->username)) {
          //  throw new Subscri('The field "name" is required.', 400);
        //}
        //self::validateSubscriName($data->username);
        //$data->description = $data->description ?? null;
      //  $status = 0;
     //   if (isset($data->status)) {
       //     $status = self::validateSubscriStatus($data->status);
        //}
        //$data->status = $status;
        //$data->userId = $data->decoded->sub;
        $Subscri = $this->getSubscriRepository()->create($data);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($Subscri->id, $Subscri->userId, $Subscri);
        }

        return $Subscri;
    }

    public function update(array $input, int $SubscriId)
    {
        $Subscri = $this->getSubscriFromDb($SubscriId, (int) $input['decoded']->sub);
        $data = json_decode(json_encode($input), false);
       // if (! isset($data->name) && ! isset($data->status)) {
        //    throw new Subscri('Enter the data to update the Subscri.', 400);
        //}
        //if (isset($data->name)) {
        //    $Subscri->name = self::validateSubscriName($data->name);
       // }
       // if (isset($data->description)) {
        //    $Subscri->description = $data->description;
        //}
        //if (isset($data->status)) {
         //   $Subscri->status = self::validateSubscriStatus($data->status);
        //}
      //  $Subscri->userId = $data->decoded->sub;
        $Subscris = $this->getSubscriRepository()->update($Subscri);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($Subscris->id, $Subscri->userId, $Subscris);
        }

        return $Subscris;
    }

    public function delete(int $SubscriId, int $userId): string
    {
        $this->getSubscriFromDb($SubscriId, $userId);
        $data = $this->getSubscriRepository()->delete($SubscriId, $userId);
        if (self::isRedisEnabled() === true) {
            $this->deleteFromCache($SubscriId, $userId);
        }

        return $data;
    }
}
