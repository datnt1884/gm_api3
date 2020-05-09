<?php

declare(strict_types=1);

namespace App\Service\LoginData;

use App\Exception\LoginData;
use Firebase\JWT\JWT;

final class LoginDataService extends Base
{
    public function getAll(): array
    {
        return $this->LoginDataRepository->getAll();
    }

    public function getOne(int $LoginDataId)
    {
        if (self::isRedisEnabled() === true) {
            $LoginData = $this->getLoginDataFromCache($LoginDataId);
        } else {
            $LoginData = $this->getLoginDataFromDb($LoginDataId);
        }

        return $LoginData;
    }

    public function search(string $LoginDatasName): array
    {
        return $this->LoginDataRepository->search($LoginDatasName);
    }

    public function create($input)
    {
        $LoginData = new \stdClass();
        $data = json_decode(json_encode($input), false);
        if (!isset($data->username)) {
            throw new LoginData('The field "username" is required.', 400);
        }
        if (!isset($data->customer_id)) {
            throw new LoginData('The field "customer_id" is required.', 400);
        }
        if (!isset($data->password)) {
            throw new LoginDataException('The field "password" is required.', 400);
        }
        $LoginData->username = self::validateLoginDataName($data->username);
        $LoginData->customer_id = $data->customer_id;
        $LoginData->password = hash('sha512', $data->password);
        //  $this->LoginDataRepository->checkLoginDataByEmail($LoginData->email);
        $LoginDatas = $this->LoginDataRepository->create($LoginData);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($LoginDatas->id, $LoginDatas);
        }

        return $LoginDatas;
    }

    public function update(array $input, int $LoginDataId)
    {
        $LoginData = $this->getLoginDataFromDb($LoginDataId);
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name) && ! isset($data->email)) {
            throw new LoginData('Enter the data to update the LoginData.', 400);
        }
        if (isset($data->name)) {
            $LoginData->name = self::validateLoginDataName($data->name);
        }

        $LoginDatas = $this->LoginDataRepository->update($LoginData);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($LoginDatas->id, $LoginDatas);
        }

        return $LoginDatas;
    }

    public function delete(int $LoginDataId): string
    {
        $this->getLoginDataFromDb($LoginDataId);
        $this->LoginDataRepository->deleteLoginDataTasks($LoginDataId);
        $data = $this->LoginDataRepository->delete($LoginDataId);
        if (self::isRedisEnabled() === true) {
            $this->deleteFromCache($LoginDataId);
        }

        return $data;
    }

    public function auth(?array $input): string
    {
        $data = json_decode(json_encode($input), false);
        if (! isset($data->LoginData_id)) {
            throw new LoginData('The field "LoginData_id" is required.', 400);
        }
        if (! isset($data->LoginData_mac_address)) {
            throw new LoginData('The field "LoginData_mac_address" is required.', 400);
        }
        //$password = hash('sha512', $data->password);
        $LoginData = $this->LoginDataRepository->loginLoginData($data->LoginData_id, $data->LoginData_mac_address);
        $token = [
            'sub' => $LoginData->id,
            'email' => $LoginData->LoginData_id,
            'mac' => $LoginData->LoginData_mac_address,
            'iat' => time(),
            'exp' => time() + (7 * 24 * 60 * 60),
        ];

        return JWT::encode($token, getenv('SECRET_KEY'));
    }
}
