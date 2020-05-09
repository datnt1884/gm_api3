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
        $query = 'SELECT * FROM `Subscris` WHERE `userId` = :userId ORDER BY `id`';
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
            INSERT INTO `Subscris` (`name`, `description`, `status`, `userId`)
            VALUES (:name, :description, :status, :userId)
        ';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('name', $Subscri->name);
        $statement->bindParam('description', $Subscri->description);
        $statement->bindParam('status', $Subscri->status);
        $statement->bindParam('userId', $Subscri->userId);
        $statement->execute();

        return $this->checkAndGetSubscri((int) $this->database->lastInsertId(), (int) $Subscri->userId);
    }

    public function update(object $Subscri): object
    {
        $query = '
            UPDATE `Subscris`
            SET `name` = :name, `description` = :description, `status` = :status
            WHERE `id` = :id AND `userId` = :userId
        ';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $Subscri->id);
        $statement->bindParam('name', $Subscri->name);
        $statement->bindParam('description', $Subscri->description);
        $statement->bindParam('status', $Subscri->status);
        $statement->bindParam('userId', $Subscri->userId);
        $statement->execute();

        return $this->checkAndGetSubscri((int) $Subscri->id, (int) $Subscri->userId);
    }

    public function delete(int $SubscriId, int $userId): string
    {
        $query = 'DELETE FROM `Subscris` WHERE `id` = :id AND `userId` = :userId';
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
