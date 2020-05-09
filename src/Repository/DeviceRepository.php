<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\Device;

final class DeviceRepository extends BaseRepository
{
    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }

    public function getDevice(int $DeviceId): object
    {
        $query = 'SELECT `id`, `name`, `email` FROM `Devices` WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam('id', $DeviceId);
        $statement->execute();
        $Device = $statement->fetchObject();
        if (! $Device) {
            throw new Device('Device not found.', 404);
        }

        return $Device;
    }

    public function checkDeviceByEmail(string $email): void
    {
        $query = 'SELECT * FROM `Devices` WHERE `email` = :email';
        $statement = $this->database->prepare($query);
        $statement->bindParam('email', $email);
        $statement->execute();
        $Device = $statement->fetchObject();
        if ($Device) {
            throw new Device('Email already exists.', 400);
        }
    }

    public function getAll(): array
    {
        $query = 'SELECT `id`, `name`, `email` FROM `Devices` ORDER BY `id`';
        $statement = $this->database->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function search(string $DevicesName): array
    {
        $query = 'SELECT `id`, `name`, `email` FROM `Devices` WHERE `name` LIKE :name ORDER BY `id`';
        $name = '%' . $DevicesName . '%';
        $statement = $this->database->prepare($query);
        $statement->bindParam('name', $name);
        $statement->execute();
        $Devices = $statement->fetchAll();
        if (! $Devices) {
            throw new Device('Device name not found.', 404);
        }

        return $Devices;
    }

    public function loginDevice(string $device_id, string $device_mac_address): object
    {
        $query = "SELECT `id`, `company_id`, `username`, `login_data_id`, `googleappid`, `device_active`, `device_id`, `device_ip`, `device_mac_address`, `device_wifimac_address`, `ntype`, `appid`, `app_name`, `app_version`, `device_brand`, `os`, `screen_resolution`, `hdmi`, `api_version`, `firmware`, `language`, `createdAt`, `updatedAt` FROM `devices` AS `devices` WHERE `devices`.`device_active` = true AND `devices`.`device_id` = :device_id AND `devices`.`device_mac_address` = :device_mac_address";
        $statement = $this->database->prepare($query);
        $statement->bindParam('device_id', $device_id);
        $statement->bindParam('device_mac_address', $device_mac_address);
        $statement->execute();
        $device = $statement->fetchObject();
        if (empty($device)) {
            throw new Device('Login failed: device_id or device_mac_address incorrect.', 400);
        }
        return $device;
    }

    public function create(object $Device): object
    {
        $query = 'INSERT INTO `Devices` (`name`, `email`, `password`) VALUES (:name, :email, :password)';
        $statement = $this->database->prepare($query);
        $statement->bindParam('name', $Device->name);
        $statement->bindParam('email', $Device->email);
        $statement->bindParam('password', $Device->password);
        $statement->execute();

        return $this->getDevice((int) $this->database->lastInsertId());
    }

    public function update(object $Device): object
    {
        $query = 'UPDATE `Devices` SET `name` = :name, `email` = :email WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam('id', $Device->id);
        $statement->bindParam('name', $Device->name);
        $statement->bindParam('email', $Device->email);
        $statement->execute();

        return $this->getDevice((int) $Device->id);
    }

    public function delete(int $DeviceId): string
    {
        $query = 'DELETE FROM `Devices` WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam('id', $DeviceId);
        $statement->execute();

        return 'The Device was deleted.';
    }

    public function deleteDeviceTasks(int $DeviceId): void
    {
        $query = 'DELETE FROM `tasks` WHERE `DeviceId` = :DeviceId';
        $statement = $this->database->prepare($query);
        $statement->bindParam('DeviceId', $DeviceId);
        $statement->execute();
    }
}
