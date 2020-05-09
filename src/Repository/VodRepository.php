<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\Vod;

final class VodRepository extends BaseRepository
{
    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }

    public function checkAndGetVod(int $VodId, int $userId): object
    {
        $query = 'SELECT * FROM `Vods` WHERE `id` = :id AND `userId` = :userId';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $VodId);
        $statement->bindParam('userId', $userId);
        $statement->execute();
        $Vod = $statement->fetchObject();
        if (! $Vod) {
            throw new Vod('Vod not found.', 404);
        }

        return $Vod;
    }

    public function getAllVods(): array
    {
        $query = 'SELECT * FROM `Vods` ORDER BY `id`';
        $statement = $this->getDb()->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getAll(int $userId): array
    {
        $query = 'SELECT * FROM `Vods` WHERE `userId` = :userId ORDER BY `id`';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function search(string $VodsName, int $userId, $status): array
    {
        $query = $this->getSearchVodsQuery($status);
        $name = '%' . $VodsName . '%';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('name', $name);
        $statement->bindParam('userId', $userId);
        if ($status === 0 || $status === 1) {
            $statement->bindParam('status', $status);
        }
        $statement->execute();

        return $statement->fetchAll();
    }

    public function create(object $Vod): object
    {
        $query = '
            INSERT INTO `Vods` (`name`, `description`, `status`, `userId`)
            VALUES (:name, :description, :status, :userId)
        ';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('name', $Vod->name);
        $statement->bindParam('description', $Vod->description);
        $statement->bindParam('status', $Vod->status);
        $statement->bindParam('userId', $Vod->userId);
        $statement->execute();

        return $this->checkAndGetVod((int) $this->database->lastInsertId(), (int) $Vod->userId);
    }

    public function update(object $Vod): object
    {
        $query = '
            UPDATE `Vods`
            SET `name` = :name, `description` = :description, `status` = :status
            WHERE `id` = :id AND `userId` = :userId
        ';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $Vod->id);
        $statement->bindParam('name', $Vod->name);
        $statement->bindParam('description', $Vod->description);
        $statement->bindParam('status', $Vod->status);
        $statement->bindParam('userId', $Vod->userId);
        $statement->execute();

        return $this->checkAndGetVod((int) $Vod->id, (int) $Vod->userId);
    }

    public function delete(int $VodId, int $userId): string
    {
        $query = 'DELETE FROM `Vods` WHERE `id` = :id AND `userId` = :userId';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $VodId);
        $statement->bindParam('userId', $userId);
        $statement->execute();

        return 'The Vod was deleted.';
    }

    private function getSearchVodsQuery($status)
    {
        $statusQuery = '';
        if ($status === 0 || $status === 1) {
            $statusQuery = 'AND `status` = :status';
        }

        return "
            SELECT * FROM `Vods`
            WHERE `name` LIKE :name AND `userId` = :userId ${statusQuery}
            ORDER BY `id`
        ";
    }
}
