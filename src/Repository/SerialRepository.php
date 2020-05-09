<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\Serial;

final class SerialRepository extends BaseRepository
{
    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }

    public function checkAndGetSerial(int $SerialId, int $userId): object
    {
        $query = 'SELECT * FROM `Serials` WHERE `id` = :id AND `userId` = :userId';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $SerialId);
        $statement->bindParam('userId', $userId);
        $statement->execute();
        $Serial = $statement->fetchObject();
        if (! $Serial) {
            throw new Serial('Serial not found.', 404);
        }

        return $Serial;
    }

    public function getAllSerials(): array
    {
        $query = 'SELECT * FROM `Serials` ORDER BY `id`';
        $statement = $this->getDb()->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getAll(int $userId): array
    {
        $query = 'SELECT * FROM `Serials` WHERE `userId` = :userId ORDER BY `id`';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function search(string $SerialsName, int $userId, $status): array
    {
        $query = $this->getSearchSerialsQuery($status);
        $name = '%' . $SerialsName . '%';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('name', $name);
        $statement->bindParam('userId', $userId);
        if ($status === 0 || $status === 1) {
            $statement->bindParam('status', $status);
        }
        $statement->execute();

        return $statement->fetchAll();
    }

    public function create(object $Serial): object
    {
        $query = '
            INSERT INTO `Serials` (`name`, `description`, `status`, `userId`)
            VALUES (:name, :description, :status, :userId)
        ';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('name', $Serial->name);
        $statement->bindParam('description', $Serial->description);
        $statement->bindParam('status', $Serial->status);
        $statement->bindParam('userId', $Serial->userId);
        $statement->execute();

        return $this->checkAndGetSerial((int) $this->database->lastInsertId(), (int) $Serial->userId);
    }

    public function update(object $Serial): object
    {
        $query = '
            UPDATE `Serials`
            SET `name` = :name, `description` = :description, `status` = :status
            WHERE `id` = :id AND `userId` = :userId
        ';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $Serial->id);
        $statement->bindParam('name', $Serial->name);
        $statement->bindParam('description', $Serial->description);
        $statement->bindParam('status', $Serial->status);
        $statement->bindParam('userId', $Serial->userId);
        $statement->execute();

        return $this->checkAndGetSerial((int) $Serial->id, (int) $Serial->userId);
    }

    public function delete(int $SerialId, int $userId): string
    {
        $query = 'DELETE FROM `Serials` WHERE `id` = :id AND `userId` = :userId';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $SerialId);
        $statement->bindParam('userId', $userId);
        $statement->execute();

        return 'The Serial was deleted.';
    }

    private function getSearchSerialsQuery($status)
    {
        $statusQuery = '';
        if ($status === 0 || $status === 1) {
            $statusQuery = 'AND `status` = :status';
        }

        return "
            SELECT * FROM `Serials`
            WHERE `name` LIKE :name AND `userId` = :userId ${statusQuery}
            ORDER BY `id`
        ";
    }
}
