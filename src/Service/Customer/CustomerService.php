<?php

declare(strict_types=1);

namespace App\Service\Customer;

use App\Exception\Customer;
use Firebase\JWT\JWT;

final class CustomerService extends Base
{
    public function getAll(): array
    {
        return $this->CustomerRepository->getAll();
    }

    public function getOne(int $CustomerId)
    {
        if (self::isRedisEnabled() === true) {
            $Customer = $this->getCustomerFromCache($CustomerId);
        } else {
            $Customer = $this->getCustomerFromDb($CustomerId);
        }

        return $Customer;
    }

    public function search(string $CustomersName): array
    {
        return $this->CustomerRepository->search($CustomersName);
    }

    public function create($input)
    {
        $data = json_decode(json_encode($input), false);
        //if (! isset($data->name)) {
        //    throw new User('The field "name" is required.', 400);
        //}
        if (! isset($data->email)) {
            throw new Customer('The field "email" is required.', 400);
        }
       // if (! isset($data->password)) {
        ////    throw new User('The field "password" is required.', 400);
       // }
        //$data->name = self::validateUserName($data->name);
        $data->email = self::validateEmail($data->email);
        //$data->password = hash('sha512', $data->password);
        $this->CustomerRepository->checkCustomerByEmail($data->email);
        $Customer = $this->CustomerRepository->create($data);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($Customer->id, $Customer);
        }

        return $Customer;
    }

    public function update(array $input, int $CustomerId)
    {
        $Customer = $this->getCustomerFromDb($CustomerId);
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name) && ! isset($data->email)) {
            throw new Customer('Enter the data to update the Customer.', 400);
        }
        if (isset($data->name)) {
            $Customer->name = self::validateCustomerName($data->name);
        }

        $Customers = $this->CustomerRepository->update($Customer);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($Customers->id, $Customers);
        }

        return $Customers;
    }

    public function delete(int $CustomerId): string
    {
        $this->getCustomerFromDb($CustomerId);
        $this->CustomerRepository->deleteCustomerTasks($CustomerId);
        $data = $this->CustomerRepository->delete($CustomerId);
        if (self::isRedisEnabled() === true) {
            $this->deleteFromCache($CustomerId);
        }

        return $data;
    }

    public function auth(?array $input): string
    {
        $data = json_decode(json_encode($input), false);
        if (! isset($data->Customer_id)) {
            throw new Customer('The field "Customer_id" is required.', 400);
        }
        if (! isset($data->Customer_mac_address)) {
            throw new Customer('The field "Customer_mac_address" is required.', 400);
        }
        //$password = hash('sha512', $data->password);
        $Customer = $this->CustomerRepository->loginCustomer($data->Customer_id, $data->Customer_mac_address);
        $token = [
            'sub' => $Customer->id,
            'email' => $Customer->Customer_id,
            'mac' => $Customer->Customer_mac_address,
            'iat' => time(),
            'exp' => time() + (7 * 24 * 60 * 60),
        ];

        return JWT::encode($token, getenv('SECRET_KEY'));
    }
}
