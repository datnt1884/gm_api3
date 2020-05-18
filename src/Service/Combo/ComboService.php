<?php

declare(strict_types=1);

namespace App\Service\Combo;

use App\Exception\Combo;

final class ComboService extends Base
{
    public function getAll(): array
    {
        return $this->ComboRepository->getCombos();
    }

    public function getOne(int $ComboId)
    {
        if (self::isRedisEnabled() === true) {
            $Combo = $this->getOneFromCache($ComboId);
        } else {
            $Combo = $this->getOneFromDb($ComboId);
        }

        return $Combo;
    }

    public function search(string $CombosName): array
    {
        return $this->ComboRepository->searchCombos($CombosName);
    }

    public function create($input)
    {
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name)) {
            throw new Combo('Invalid data: name is required.', 400);
        }
        self::validateComboName($data->name);
        $data->description = $data->description ?? null;
        $Combo = $this->ComboRepository->createCombo($data);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($Combo->id, $Combo);
        }

        return $Combo;
    }

    public function update($input, int $ComboId)
    {
        $Combo = $this->getOneFromDb($ComboId);
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name) && ! isset($data->description)) {
            throw new Combo('Enter the data to update the Combo.', 400);
        }
        if (isset($data->name)) {
            $Combo->name = self::validateComboName($data->name);
        }
        if (isset($data->description)) {
            $Combo->description = $data->description;
        }
        $Combos = $this->ComboRepository->updateCombo($Combo);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($Combos->id, $Combos);
        }

        return $Combos;
    }

    public function delete(int $ComboId): void
    {
        $this->getOneFromDb($ComboId);
        $this->ComboRepository->deleteCombo($ComboId);
        if (self::isRedisEnabled() === true) {
            $this->deleteFromCache($ComboId);
        }
    }
}
