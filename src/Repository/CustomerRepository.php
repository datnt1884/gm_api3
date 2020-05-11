<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\Customer;

final class CustomerRepository extends BaseRepository
{
    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }

    public function getCustomer(int $CustomerId): object
    {
        $query = 'SELECT * FROM `customer_data` WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam('id', $CustomerId);
        $statement->execute();
        $Customer = $statement->fetchObject();
        if (! $Customer) {
            throw new Customer('Customer not found.', 404);
        }

        return $Customer;
    }

    public function checkCustomerByEmail(string $email): void
    {
        $query = 'SELECT * FROM `customer_data` WHERE `email` = :email';
        $statement = $this->database->prepare($query);
        $statement->bindParam('email', $email);
        $statement->execute();
        $Customer = $statement->fetchObject();
        if ($Customer) {
            throw new Customer('Email already exists.', 400);
        }
    }

    public function getAll(): array
    {
        $query = 'SELECT `id`, `name`, `email` FROM `Customers` ORDER BY `id`';
        $statement = $this->database->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function search(string $CustomersName): array
    {
        $query = 'SELECT `id`, `name`, `email` FROM `Customers` WHERE `name` LIKE :name ORDER BY `id`';
        $name = '%' . $CustomersName . '%';
        $statement = $this->database->prepare($query);
        $statement->bindParam('name', $name);
        $statement->execute();
        $Customers = $statement->fetchAll();
        if (! $Customers) {
            throw new Customer('Customer name not found.', 404);
        }

        return $Customers;
    }

    public function loginCustomer(string $Customer_id, string $Customer_mac_address): object
    {
        $query = "SELECT `id`, `company_id`, `username`, `login_data_id`, `googleappid`, `Customer_active`, `Customer_id`, `Customer_ip`, `Customer_mac_address`, `Customer_wifimac_address`, `ntype`, `appid`, `app_name`, `app_version`, `Customer_brand`, `os`, `screen_resolution`, `hdmi`, `api_version`, `firmware`, `language`, `createdAt`, `updatedAt` FROM `Customers` AS `Customers` WHERE `Customers`.`Customer_active` = true AND `Customers`.`Customer_id` = :Customer_id AND `Customers`.`Customer_mac_address` = :Customer_mac_address";
        $statement = $this->database->prepare($query);
        $statement->bindParam('Customer_id', $Customer_id);
        $statement->bindParam('Customer_mac_address', $Customer_mac_address);
        $statement->execute();
        $Customer = $statement->fetchObject();
        if (empty($Customer)) {
            throw new Customer('Login failed: Customer_id or Customer_mac_address incorrect.', 400);
        }
        return $Customer;
    }

    public function create(object $Customer): object
    {
        $query = "INSERT INTO `customer_data` (`company_id`,`group_id`,`firstname`,`lastname`,`email`,`address`,`city`,`country`,`zip_code`,`telephone`) 
                  VALUES (1,1,:firstname,:lastname,:email,'ha noi','ha noi',' Vietnam','','1234567890')
                  ";
        $statement = $this->database->prepare($query);

        $statement->bindParam('firstname', $Customer->firstname);
        $statement->bindParam('lastname', $Customer->lastname);
        $statement->bindParam('email', $Customer->email);
        //  $statement->bindParam('password', $Customer->password);
        $statement->execute();

        return $this->getCustomer((int) $this->database->lastInsertId());
    }

    public function update(object $Customer): object
    {
        $query = 'UPDATE `Customers` SET `name` = :name, `email` = :email WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam('id', $Customer->id);
        $statement->bindParam('name', $Customer->name);
        $statement->bindParam('email', $Customer->email);
        $statement->execute();

        return $this->getCustomer((int) $Customer->id);
    }

    public function delete(int $CustomerId): string
    {
        $query = 'DELETE FROM `Customers` WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam('id', $CustomerId);
        $statement->execute();

        return 'The Customer was deleted.';
    }

    public function deleteCustomerTasks(int $CustomerId): void
    {
        $query = 'DELETE FROM `tasks` WHERE `CustomerId` = :CustomerId';
        $statement = $this->database->prepare($query);
        $statement->bindParam('CustomerId', $CustomerId);
        $statement->execute();
    }
}
