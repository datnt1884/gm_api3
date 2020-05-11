<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\LoginData;

final class LoginDataRepository extends BaseRepository
{
    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }

    public function getLoginData(int $LoginDataId): object
    {
        $query = 'SELECT * FROM `login_data` WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam('id', $LoginDataId);
        $statement->execute();
        $LoginData = $statement->fetchObject();
        if (! $LoginData) {
            throw new LoginData('LoginData not found.', 404);
        }

        return $LoginData;
    }

    public function checkLoginDataByEmail(string $email): void
    {
        $query = 'SELECT * FROM `login_data` WHERE `email` = :email';
        $statement = $this->database->prepare($query);
        $statement->bindParam('email', $email);
        $statement->execute();
        $LoginData = $statement->fetchObject();
        if ($LoginData) {
            throw new LoginData('Email already exists.', 400);
        }
    }

    public function getAll(): array
    {
        $query = 'SELECT `id`, `name`, `email` FROM `LoginDatas` ORDER BY `id`';
        $statement = $this->database->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function search(string $LoginDatasName): array
    {
        $query = 'SELECT `id`, `name`, `email` FROM `LoginDatas` WHERE `name` LIKE :name ORDER BY `id`';
        $name = '%' . $LoginDatasName . '%';
        $statement = $this->database->prepare($query);
        $statement->bindParam('name', $name);
        $statement->execute();
        $LoginDatas = $statement->fetchAll();
        if (! $LoginDatas) {
            throw new LoginData('LoginData name not found.', 404);
        }

        return $LoginDatas;
    }

    public function loginLoginData(string $LoginData_id, string $LoginData_mac_address): object
    {
        $query = "SELECT `id`, `company_id`, `username`, `login_data_id`, `googleappid`, `LoginData_active`, `LoginData_id`, `LoginData_ip`, `LoginData_mac_address`, `LoginData_wifimac_address`, `ntype`, `appid`, `app_name`, `app_version`, `LoginData_brand`, `os`, `screen_resolution`, `hdmi`, `api_version`, `firmware`, `language`, `createdAt`, `updatedAt` FROM `LoginDatas` AS `LoginDatas` WHERE `LoginDatas`.`LoginData_active` = true AND `LoginDatas`.`LoginData_id` = :LoginData_id AND `LoginDatas`.`LoginData_mac_address` = :LoginData_mac_address";
        $statement = $this->database->prepare($query);
        $statement->bindParam('LoginData_id', $LoginData_id);
        $statement->bindParam('LoginData_mac_address', $LoginData_mac_address);
        $statement->execute();
        $LoginData = $statement->fetchObject();
        if (empty($LoginData)) {
            throw new LoginData('Login failed: LoginData_id or LoginData_mac_address incorrect.', 400);
        }
        return $LoginData;
    }

    public function create(object $LoginData): object
    {
        $query = " INSERT INTO `login_data` (`company_id`,`username`,`mac_address`,`password`,`salt`,`customer_id`,`channel_stream_source_id`,`vod_stream_source`,`pin`,`show_adult`,`auto_timezone`,`timezone`,`player`,`activity_timeout`,`get_messages`,`get_ads`,`resetPasswordToken`,`resetPasswordExpires`,`vodlastchange`,`livetvlastchange`,`account_lock`,`beta_user`)
                  VALUES (1,:username,NULL,:password,'1uLcrD2FGEvgLlI5Zc/xQg==',:customer_id,1,1,'1234',false,false,-11,'default',10800,false,false,' ','0',1583727216363,1583727216363,false,false)
                  ";
        $statement = $this->database->prepare($query);
        $statement->bindParam("username", $LoginData->username);
        $statement->bindParam("password", $LoginData->password);
        $statement->bindParam("customer_id", $LoginData->customer_id);
        $statement->execute();
        return $this->getLoginData((int) $this->database->lastInsertId());
    }

    public function update(object $LoginData): object
    {
        $query = 'UPDATE `LoginDatas` SET `name` = :name, `email` = :email WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam('id', $LoginData->id);
        $statement->bindParam('name', $LoginData->name);
        $statement->bindParam('email', $LoginData->email);
        $statement->execute();

        return $this->getLoginData((int) $LoginData->id);
    }

    public function delete(int $LoginDataId): string
    {
        $query = 'DELETE FROM `LoginDatas` WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam('id', $LoginDataId);
        $statement->execute();

        return 'The LoginData was deleted.';
    }

    public function deleteLoginDataTasks(int $LoginDataId): void
    {
        $query = 'DELETE FROM `tasks` WHERE `LoginDataId` = :LoginDataId';
        $statement = $this->database->prepare($query);
        $statement->bindParam('LoginDataId', $LoginDataId);
        $statement->execute();
    }
}
