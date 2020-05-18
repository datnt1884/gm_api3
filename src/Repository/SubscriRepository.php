<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\Subscri;

final class SubscriRepository extends BaseRepository
{
    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }

    public function checkAndGetSubscri(int $SubscriId, int $userId): object
    {
        $query = 'SELECT * FROM `Subscris` WHERE `id` = :id AND `userId` = :userId';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $SubscriId);
        $statement->bindParam('userId', $userId);
        $statement->execute();
        $Subscri = $statement->fetchObject();
        if (! $Subscri) {
            throw new Subscri('Subscri not found.', 404);
        }

        return $Subscri;
    }

    public function getAllSubscris(): array
    {
        $query = 'SELECT * FROM `Subscris` ORDER BY `id`';
        $statement = $this->getDb()->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getAll(int $userId): array
    {
        $query = " SELECT * FROM `subscription`  
                   WHERE `package_id` = '1' AND `company_id` = '1' AND `login_id` = :userId 
                   ORDER BY `id`";
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function search(string $SubscrisName, int $userId, $status): array
    {
        $query = $this->getSearchSubscrisQuery($status);
        $name = '%' . $SubscrisName . '%';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('name', $name);
        $statement->bindParam('userId', $userId);
        if ($status === 0 || $status === 1) {
            $statement->bindParam('status', $status);
        }
        $statement->execute();

        return $statement->fetchAll();
    }

    public function create(object $Subscri): object
    {
        $query = '
            INSERT INTO `subscription` (`company_id`, `login_id`, `package_id`, `customer_username`, `user_username`, `start_date`, `end_date`)
            VALUES (:company_id, :login_id, :package_id, :customer_username, :user_username, :start_date, :end_date )
        ';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('company_id', $Subscri->company_id);
        $statement->bindParam('login_id', $Subscri->login_id);
        $statement->bindParam('package_id', $Subscri->package_id);
        $statement->bindParam('customer_username', $Subscri->customer_username);
        $statement->bindParam('user_username', $Subscri->user_username);
        $statement->bindParam('start_date', $Subscri->start_date);
        $statement->bindParam('end_date', $Subscri->end_date);
        $statement->execute();
        return $this->checkAndGetSubscri((int) $this->database->lastInsertId(), (int) $Subscri->login_id);
    }

    public function update(object $Subscri): object
    {
        $query = '
            UPDATE `Subscris`
            SET `company_id` = :company_id,  `package_id` = :package_id, `en_date` =:en_date
            WHERE `id` = :id AND `login_id` = :login_id
        ';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $Subscri->id);
        $statement->bindParam('company_id', $Subscri->company_id);
        $statement->bindParam('package_id', $Subscri->package_id);
        $statement->bindParam('end_date', $Subscri->end_date);
        $statement->bindParam('login_id', $Subscri->login_id);
        $statement->execute();

        return $this->checkAndGetSubscri((int) $Subscri->id, (int) $Subscri->login_id);
    }

    public function delete(int $SubscriId, int $userId): string
    {
        $query = 'DELETE FROM `Subscris` WHERE `id` = :id AND `login_id` = :login_id';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $SubscriId);
        $statement->bindParam('userId', $userId);
        $statement->execute();

        return 'The Subscri was deleted.';
    }

    private function getSearchSubscrisQuery($status)
    {
        $statusQuery = '';
        if ($status === 0 || $status === 1) {
            $statusQuery = 'AND `status` = :status';
        }

        return "
            SELECT * FROM `Subscris`
            WHERE `name` LIKE :name AND `userId` = :userId ${statusQuery}
            ORDER BY `id`
        ";
    }
}
