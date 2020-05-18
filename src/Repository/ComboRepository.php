<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\Combo;

final class ComboRepository extends BaseRepository
{
    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }

    public function checkAndGetCombo(int $ComboId): object
    {
        $query = 'SELECT * FROM `combo` WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam(':id', $ComboId);
        $statement->execute();
        $Combo = $statement->fetchObject();
        if (! $Combo) {
            throw new Combo('Combo not found.', 404);
        }

        return $Combo;
    }

    public function getCombos(): array
    {
        $query = 'SELECT * FROM `combo` ORDER BY `id`';
        $statement = $this->database->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function searchCombos(string $strCombos): array
    {
        $query = 'SELECT * FROM `Combos` WHERE `name` LIKE :name OR `description` LIKE :description ORDER BY `id`';
        $name = '%' . $strCombos . '%';
        $description = '%' . $strCombos . '%';
        $statement = $this->database->prepare($query);
        $statement->bindParam('name', $name);
        $statement->bindParam('description', $description);
        $statement->execute();
        $Combos = $statement->fetchAll();
        if (! $Combos) {
            throw new Combo('No Combos with that name or description were found.', 404);
        }

        return $Combos;
    }

    public function createCombo(object $data)
    {
        $query = 'INSERT INTO `Combos` (`name`, `description`) VALUES (:name, :description)';
        $statement = $this->database->prepare($query);
        $statement->bindParam(':name', $data->name);
        $statement->bindParam(':description', $data->description);
        $statement->execute();

        return $this->checkAndGetCombo((int) $this->database->lastInsertId());
    }

    public function updateCombo(object $Combo): object
    {
        $query = 'UPDATE `Combos` SET `name` = :name, `description` = :description WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam(':id', $Combo->id);
        $statement->bindParam(':name', $Combo->name);
        $statement->bindParam(':description', $Combo->description);
        $statement->execute();

        return $this->checkAndGetCombo((int) $Combo->id);
    }

    public function deleteCombo(int $ComboId): void
    {
        $query = 'DELETE FROM `Combos` WHERE `id` = :id';
        $statement = $this->database->prepare($query);
        $statement->bindParam(':id', $ComboId);
        $statement->execute();
    }
}
