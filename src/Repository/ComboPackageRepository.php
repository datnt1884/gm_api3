<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\ComboPackage;

final class ComboPackageRepository extends BaseRepository
{
    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }

    public function checkAndGetComboPackage(int $ComboPackageId): object
    {
        $query = 'SELECT * FROM `combo_packages` WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam(':id', $ComboPackageId);
        $statement->execute();
        $ComboPackage = $statement->fetchObject();
        if (! $ComboPackage) {
            throw new ComboPackage('ComboPackage not found.', 404);
        }

        return $ComboPackage;
    }
    public function getByComboId(int $combo_id): array
    {
        $query = 'SELECT * FROM `combo_packages` WHERE `combo_id` = :combo_id ORDER BY `id`';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('combo_id', $combo_id);
        $statement->execute();

        return $statement->fetchAll();
    }
    public function getComboPackages(): array
    {
        $query = 'SELECT * FROM `combo_packages` ORDER BY `id`';
        $statement = $this->database->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function searchComboPackages(string $strComboPackages): array
    {
        $query = 'SELECT * FROM `ComboPackages` WHERE `name` LIKE :name OR `description` LIKE :description ORDER BY `id`';
        $name = '%' . $strComboPackages . '%';
        $description = '%' . $strComboPackages . '%';
        $statement = $this->database->prepare($query);
        $statement->bindParam('name', $name);
        $statement->bindParam('description', $description);
        $statement->execute();
        $ComboPackages = $statement->fetchAll();
        if (! $ComboPackages) {
            throw new ComboPackage('No ComboPackages with that name or description were found.', 404);
        }

        return $ComboPackages;
    }

    public function createComboPackage(object $data)
    {
        $query = 'INSERT INTO `ComboPackages` (`name`, `description`) VALUES (:name, :description)';
        $statement = $this->database->prepare($query);
        $statement->bindParam(':name', $data->name);
        $statement->bindParam(':description', $data->description);
        $statement->execute();

        return $this->checkAndGetComboPackage((int) $this->database->lastInsertId());
    }

    public function updateComboPackage(object $ComboPackage): object
    {
        $query = 'UPDATE `ComboPackages` SET `name` = :name, `description` = :description WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam(':id', $ComboPackage->id);
        $statement->bindParam(':name', $ComboPackage->name);
        $statement->bindParam(':description', $ComboPackage->description);
        $statement->execute();

        return $this->checkAndGetComboPackage((int) $ComboPackage->id);
    }

    public function deleteComboPackage(int $ComboPackageId): void
    {
        $query = 'DELETE FROM `ComboPackages` WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam(':id', $ComboPackageId);
        $statement->execute();
    }
}
