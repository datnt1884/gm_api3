<?php

declare(strict_types=1);

namespace App\Service\Serial;

use App\Exception\Serial;

final class SerialService extends Base
{
    public function getAllSerials(): array
    {
        return $this->getSerialRepository()->getAllSerials();
    }

    public function getAll(int $userId): array
    {
        return $this->getSerialRepository()->getAll($userId);
    }

    public function getOne(int $SerialId, int $userId)
    {
        if (self::isRedisEnabled() === true) {
            $Serial = $this->getSerialFromCache($SerialId, $userId);
        } else {
            $Serial = $this->getSerialFromDb($SerialId, $userId);
        }

        return $Serial;
    }

    public function search(string $SerialsName, int $userId, $status): array
    {
        if ($status !== null) {
            $status = (int) $status;
        }

        return $this->getSerialRepository()->search($SerialsName, $userId, $status);
    }

    public function create(array $input)
    {
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name)) {
            throw new Serial('The field "name" is required.', 400);
        }
        self::validateSerialName($data->name);
        $data->description = $data->description ?? null;
        $status = 0;
        if (isset($data->status)) {
            $status = self::validateSerialStatus($data->status);
        }
        $data->status = $status;
        $data->userId = $data->decoded->sub;
        $Serial = $this->getSerialRepository()->create($data);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($Serial->id, $Serial->userId, $Serial);
        }

        return $Serial;
    }

    public function update(array $input, int $SerialId)
    {
        $Serial = $this->getSerialFromDb($SerialId, (int) $input['decoded']->sub);
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name) && ! isset($data->status)) {
            throw new Serial('Enter the data to update the Serial.', 400);
        }
        if (isset($data->name)) {
            $Serial->name = self::validateSerialName($data->name);
        }
        if (isset($data->description)) {
            $Serial->description = $data->description;
        }
        if (isset($data->status)) {
            $Serial->status = self::validateSerialStatus($data->status);
        }
        $Serial->userId = $data->decoded->sub;
        $Serials = $this->getSerialRepository()->update($Serial);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($Serials->id, $Serial->userId, $Serials);
        }

        return $Serials;
    }

    public function delete(int $SerialId, int $userId): string
    {
        $this->getSerialFromDb($SerialId, $userId);
        $data = $this->getSerialRepository()->delete($SerialId, $userId);
        if (self::isRedisEnabled() === true) {
            $this->deleteFromCache($SerialId, $userId);
        }

        return $data;
    }
}
