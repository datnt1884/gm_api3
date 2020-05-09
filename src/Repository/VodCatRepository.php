<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\VodCat;

final class VodCatRepository extends BaseRepository
{
    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }

    public function getVodCat(int $VodCatId): object
    {
        $query = 'SELECT `id`, `name`, `email` FROM `VodCats` WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam('id', $VodCatId);
        $statement->execute();
        $VodCat = $statement->fetchObject();
        if (! $VodCat) {
            throw new VodCat('VodCat not found.', 404);
        }

        return $VodCat;
    }

    public function checkVodCatByEmail(string $email): void
    {
        $query = 'SELECT * FROM `VodCats` WHERE `email` = :email';
        $statement = $this->database->prepare($query);
        $statement->bindParam('email', $email);
        $statement->execute();
        $VodCat = $statement->fetchObject();
        if ($VodCat) {
            throw new VodCat('Email already exists.', 400);
        }
    }

    public function getAll(): array
    {
        $query = " SELECT * FROM `vod_category` WHERE `company_id` ='1' AND `isavailable` ='1'";
        $statement = $this->getDb()->prepare($query);
        $statement->execute();
        return $statement->fetchAll();
    }

    public function search(string $VodCatsName): array
    {
        $query = 'SELECT `id`, `name`, `email` FROM `VodCats` WHERE `name` LIKE :name ORDER BY `id`';
        $name = '%' . $VodCatsName . '%';
        $statement = $this->database->prepare($query);
        $statement->bindParam('name', $name);
        $statement->execute();
        $VodCats = $statement->fetchAll();
        if (! $VodCats) {
            throw new VodCat('VodCat name not found.', 404);
        }

        return $VodCats;
    }



    public function create(object $VodCat): object
    {
        $query = 'INSERT INTO `VodCats` (`name`, `email`, `password`) VALUES (:name, :email, :password)';
        $statement = $this->database->prepare($query);
        $statement->bindParam('name', $VodCat->name);
        $statement->bindParam('email', $VodCat->email);
        $statement->bindParam('password', $VodCat->password);
        $statement->execute();

        return $this->getVodCat((int) $this->database->lastInsertId());
    }

    public function update(object $VodCat): object
    {
        $query = 'UPDATE `VodCats` SET `name` = :name, `email` = :email WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam('id', $VodCat->id);
        $statement->bindParam('name', $VodCat->name);
        $statement->bindParam('email', $VodCat->email);
        $statement->execute();

        return $this->getVodCat((int) $VodCat->id);
    }

    public function delete(int $VodCatId): string
    {
        $query = 'DELETE FROM `VodCats` WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam('id', $VodCatId);
        $statement->execute();

        return 'The VodCat was deleted.';
    }

    public function deleteVodCatTasks(int $VodCatId): void
    {
        $query = 'DELETE FROM `tasks` WHERE `VodCatId` = :VodCatId';
        $statement = $this->database->prepare($query);
        $statement->bindParam('VodCatId', $VodCatId);
        $statement->execute();
    }
}
