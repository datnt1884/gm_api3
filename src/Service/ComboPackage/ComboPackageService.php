<?php

declare(strict_types=1);

namespace App\Service\ComboPackage;

use App\Exception\ComboPackage;

final class ComboPackageService extends Base
{
    public function getAll(): array
    {
        return $this->ComboPackageRepository->getComboPackages();
    }
    public function getByComboId($combo_id)
    {
        return $this->ComboPackageRepository->getByComboId($combo_id);
    }

    public function getOne(int $ComboPackageId)
    {
        if (self::isRedisEnabled() === true) {
            $ComboPackage = $this->getOneFromCache($ComboPackageId);
        } else {
            $ComboPackage = $this->getOneFromDb($ComboPackageId);
        }

        return $ComboPackage;
    }

    public function search(string $ComboPackagesName): array
    {
        return $this->ComboPackageRepository->searchComboPackages($ComboPackagesName);
    }

    public function create($input)
    {
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name)) {
            throw new ComboPackage('Invalid data: name is required.', 400);
        }
        self::validateComboPackageName($data->name);
        $data->description = $data->description ?? null;
        $ComboPackage = $this->ComboPackageRepository->createComboPackage($data);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($ComboPackage->id, $ComboPackage);
        }

        return $ComboPackage;
    }

    public function update($input, int $ComboPackageId)
    {
        $ComboPackage = $this->getOneFromDb($ComboPackageId);
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name) && ! isset($data->description)) {
            throw new ComboPackage('Enter the data to update the ComboPackage.', 400);
        }
        if (isset($data->name)) {
            $ComboPackage->name = self::validateComboPackageName($data->name);
        }
        if (isset($data->description)) {
            $ComboPackage->description = $data->description;
        }
        $ComboPackages = $this->ComboPackageRepository->updateComboPackage($ComboPackage);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($ComboPackages->id, $ComboPackages);
        }

        return $ComboPackages;
    }

    public function delete(int $ComboPackageId): void
    {
        $this->getOneFromDb($ComboPackageId);
        $this->ComboPackageRepository->deleteComboPackage($ComboPackageId);
        if (self::isRedisEnabled() === true) {
            $this->deleteFromCache($ComboPackageId);
        }
    }
}
