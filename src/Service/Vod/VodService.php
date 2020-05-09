<?php

declare(strict_types=1);

namespace App\Service\Vod;

use App\Exception\Vod;

final class VodService extends Base
{
    public function getAllVods(): array
    {
        return $this->getVodRepository()->getAllVods();
    }

    public function getAll(int $userId): array
    {
        return $this->getVodRepository()->getAll($userId);
    }

    public function getOne(int $VodId, int $userId)
    {
        if (self::isRedisEnabled() === true) {
            $Vod = $this->getVodFromCache($VodId, $userId);
        } else {
            $Vod = $this->getVodFromDb($VodId, $userId);
        }

        return $Vod;
    }

    public function search(string $VodsName, int $userId, $status): array
    {
        if ($status !== null) {
            $status = (int) $status;
        }

        return $this->getVodRepository()->search($VodsName, $userId, $status);
    }

    public function create(array $input)
    {
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name)) {
            throw new Vod('The field "name" is required.', 400);
        }
        self::validateVodName($data->name);
        $data->description = $data->description ?? null;
        $status = 0;
        if (isset($data->status)) {
            $status = self::validateVodStatus($data->status);
        }
        $data->status = $status;
        $data->userId = $data->decoded->sub;
        $Vod = $this->getVodRepository()->create($data);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($Vod->id, $Vod->userId, $Vod);
        }

        return $Vod;
    }

    public function update(array $input, int $VodId)
    {
        $Vod = $this->getVodFromDb($VodId, (int) $input['decoded']->sub);
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name) && ! isset($data->status)) {
            throw new Vod('Enter the data to update the Vod.', 400);
        }
        if (isset($data->name)) {
            $Vod->name = self::validateVodName($data->name);
        }
        if (isset($data->description)) {
            $Vod->description = $data->description;
        }
        if (isset($data->status)) {
            $Vod->status = self::validateVodStatus($data->status);
        }
        $Vod->userId = $data->decoded->sub;
        $Vods = $this->getVodRepository()->update($Vod);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($Vods->id, $Vod->userId, $Vods);
        }

        return $Vods;
    }

    public function delete(int $VodId, int $userId): string
    {
        $this->getVodFromDb($VodId, $userId);
        $data = $this->getVodRepository()->delete($VodId, $userId);
        if (self::isRedisEnabled() === true) {
            $this->deleteFromCache($VodId, $userId);
        }

        return $data;
    }
}
