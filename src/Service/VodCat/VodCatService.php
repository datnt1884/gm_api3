<?php

declare(strict_types=1);

namespace App\Service\VodCat;

use App\Exception\VodCat;
use Firebase\JWT\JWT;

final class VodCatService extends Base
{
    public function getAll(): array
    {
        return $this->VodCatRepository->getAll();
    }

    public function getOne(int $VodCatId)
    {
        if (self::isRedisEnabled() === true) {
            $VodCat = $this->getVodCatFromCache($VodCatId);
        } else {
            $VodCat = $this->getVodCatFromDb($VodCatId);
        }

        return $VodCat;
    }

    public function search(string $VodCatsName): array
    {
        return $this->VodCatRepository->search($VodCatsName);
    }

    public function create($input)
    {
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name)) {
            throw new VodCat('The field "name" is required.', 400);
        }
        if (! isset($data->email)) {
            throw new VodCat('The field "email" is required.', 400);
        }
        if (! isset($data->password)) {
            throw new VodCat('The field "password" is required.', 400);
        }
        $data->name = self::validateVodCatName($data->name);
        $data->email = self::validateEmail($data->email);
        $data->password = hash('sha512', $data->password);
        $this->VodCatRepository->checkVodCatByEmail($data->email);
        $VodCat = $this->VodCatRepository->create($data);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($VodCat->id, $VodCat);
        }

        return $VodCat;
    }

    public function update(array $input, int $VodCatId)
    {
        $VodCat = $this->getVodCatFromDb($VodCatId);
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name) && ! isset($data->email)) {
            throw new VodCat('Enter the data to update the VodCat.', 400);
        }
        if (isset($data->name)) {
            $VodCat->name = self::validateVodCatName($data->name);
        }
        if (isset($data->email)) {
            $VodCat->email = self::validateEmail($data->email);
        }
        $VodCats = $this->VodCatRepository->update($VodCat);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($VodCats->id, $VodCats);
        }

        return $VodCats;
    }

    public function delete(int $VodCatId): string
    {
        $this->getVodCatFromDb($VodCatId);
        $this->VodCatRepository->deleteVodCatTasks($VodCatId);
        $data = $this->VodCatRepository->delete($VodCatId);
        if (self::isRedisEnabled() === true) {
            $this->deleteFromCache($VodCatId);
        }

        return $data;
    }


}
