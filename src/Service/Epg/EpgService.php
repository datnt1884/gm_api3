<?php

declare(strict_types=1);

namespace App\Service\Epg;

use App\Exception\Epg;

final class EpgService extends Base
{
    public function getAllEpgs(): array
    {
        return $this->getEpgRepository()->getAllEpgs();
    }

    public function getAll(int $userId): array
    {
        return $this->getEpgRepository()->getAll($userId);
    }
    public function getCatchup($input): array
    {
        return $this->getEpgRepository()->getCatchup($input);
    }
    public function getEpg($input): array
    {
        return $this->getEpgRepository()->getEpg($input);
    }
    public function getOne(int $EpgId, int $userId)
    {
        if (self::isRedisEnabled() === true) {
            $Epg = $this->getEpgFromCache($EpgId, $userId);
        } else {
            $Epg = $this->getEpgFromDb($EpgId, $userId);
        }

        return $Epg;
    }

    public function search(string $EpgsName, int $userId, $status): array
    {
        if ($status !== null) {
            $status = (int) $status;
        }

        return $this->getEpgRepository()->search($EpgsName, $userId, $status);
    }

    public function create(array $input)
    {
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name)) {
            throw new Epg('The field "name" is required.', 400);
        }
        self::validateEpgName($data->name);
        $data->description = $data->description ?? null;
        $status = 0;
        if (isset($data->status)) {
            $status = self::validateEpgStatus($data->status);
        }
        $data->status = $status;
        $data->userId = $data->decoded->sub;
        $Epg = $this->getEpgRepository()->create($data);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($Epg->id, $Epg->userId, $Epg);
        }

        return $Epg;
    }

    public function update(array $input, int $EpgId)
    {
        $Epg = $this->getEpgFromDb($EpgId, (int) $input['decoded']->sub);
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name) && ! isset($data->status)) {
            throw new Epg('Enter the data to update the Epg.', 400);
        }
        if (isset($data->name)) {
            $Epg->name = self::validateEpgName($data->name);
        }
        if (isset($data->description)) {
            $Epg->description = $data->description;
        }
        if (isset($data->status)) {
            $Epg->status = self::validateEpgStatus($data->status);
        }
        $Epg->userId = $data->decoded->sub;
        $Epgs = $this->getEpgRepository()->update($Epg);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($Epgs->id, $Epg->userId, $Epgs);
        }

        return $Epgs;
    }

    public function delete(int $EpgId, int $userId): string
    {
        $this->getEpgFromDb($EpgId, $userId);
        $data = $this->getEpgRepository()->delete($EpgId, $userId);
        if (self::isRedisEnabled() === true) {
            $this->deleteFromCache($EpgId, $userId);
        }

        return $data;
    }
}
