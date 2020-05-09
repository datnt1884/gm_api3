<?php

declare(strict_types=1);

namespace App\Service\Device;

use App\Exception\Device;
use Firebase\JWT\JWT;

final class DeviceService extends Base
{
    public function getAll(): array
    {
        return $this->DeviceRepository->getAll();
    }

    public function getOne(int $DeviceId)
    {
        if (self::isRedisEnabled() === true) {
            $Device = $this->getDeviceFromCache($DeviceId);
        } else {
            $Device = $this->getDeviceFromDb($DeviceId);
        }

        return $Device;
    }

    public function search(string $DevicesName): array
    {
        return $this->DeviceRepository->search($DevicesName);
    }

    public function create($input)
    {
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name)) {
            throw new Device('The field "name" is required.', 400);
        }

        if (! isset($data->password)) {
            throw new Device('The field "password" is required.', 400);
        }
        $data->name = self::validateDeviceName($data->name);
        $data->password = hash('sha512', $data->password);
        $Device = $this->DeviceRepository->create($data);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($Device->id, $Device);
        }

        return $Device;
    }

    public function update(array $input, int $DeviceId)
    {
        $Device = $this->getDeviceFromDb($DeviceId);
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name) && ! isset($data->email)) {
            throw new Device('Enter the data to update the Device.', 400);
        }
        if (isset($data->name)) {
            $Device->name = self::validateDeviceName($data->name);
        }

        $Devices = $this->DeviceRepository->update($Device);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($Devices->id, $Devices);
        }

        return $Devices;
    }

    public function delete(int $DeviceId): string
    {
        $this->getDeviceFromDb($DeviceId);
        $this->DeviceRepository->deleteDeviceTasks($DeviceId);
        $data = $this->DeviceRepository->delete($DeviceId);
        if (self::isRedisEnabled() === true) {
            $this->deleteFromCache($DeviceId);
        }

        return $data;
    }

    public function auth(?array $input): string
    {
        $data = json_decode(json_encode($input), false);
        if (! isset($data->device_id)) {
            throw new Device('The field "device_id" is required.', 400);
        }
        if (! isset($data->device_mac_address)) {
            throw new Device('The field "device_mac_address" is required.', 400);
        }
        //$password = hash('sha512', $data->password);
        $Device = $this->DeviceRepository->loginDevice($data->device_id, $data->device_mac_address);
        $token = [
            'sub' => $Device->id,
            'email' => $Device->device_id,
            'mac' => $Device->device_mac_address,
            'iat' => time(),
            'exp' => time() + (7 * 24 * 60 * 60),
        ];

        return JWT::encode($token, getenv('SECRET_KEY'));
    }
}
