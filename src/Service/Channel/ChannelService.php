<?php

declare(strict_types=1);

namespace App\Service\Channel;

use App\Exception\Channel;

final class ChannelService extends Base
{


    public function getAllChannels(): array
    {
        return $this->getChannelRepository()->getAllChannels();
    }

    public function getAll(int $userId): array
    {
        return $this->getChannelRepository()->getAll($userId);
    }
    public function getCatchup(int $userId): array
    {
        return $this->getChannelRepository()->getCatchup($userId);
    }
    public function getCatChannels(int $userId, int $genreId): array
    {
        return $this->getChannelRepository()->getCatChannels($userId,$genreId);
    }




    public function getOne(int $ChannelId, int $userId)
    {
        if (self::isRedisEnabled() === true) {
            $Channel = $this->getChannelFromCache($ChannelId, $userId);
        } else {
            $Channel = $this->getChannelFromDb($ChannelId, $userId);
        }

        return $Channel;
    }

    public function search(string $ChannelsName, int $userId, $status): array
    {
        if ($status !== null) {
            $status = (int) $status;
        }

        return $this->getChannelRepository()->search($ChannelsName, $userId, $status);
    }

    public function create(array $input)
    {
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name)) {
            throw new Channel('The field "name" is required.', 400);
        }
        self::validateChannelName($data->name);
        $data->description = $data->description ?? null;
        $status = 0;
        if (isset($data->status)) {
            $status = self::validateChannelStatus($data->status);
        }
        $data->status = $status;
        $data->userId = $data->decoded->sub;
        $Channel = $this->getChannelRepository()->create($data);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($Channel->id, $Channel->userId, $Channel);
        }

        return $Channel;
    }

    public function update(array $input, int $ChannelId)
    {
        $Channel = $this->getChannelFromDb($ChannelId, (int) $input['decoded']->sub);
        $data = json_decode(json_encode($input), false);
        if (! isset($data->name) && ! isset($data->status)) {
            throw new Channel('Enter the data to update the Channel.', 400);
        }
        if (isset($data->name)) {
            $Channel->name = self::validateChannelName($data->name);
        }
        if (isset($data->description)) {
            $Channel->description = $data->description;
        }
        if (isset($data->status)) {
            $Channel->status = self::validateChannelStatus($data->status);
        }
        $Channel->userId = $data->decoded->sub;
        $Channels = $this->getChannelRepository()->update($Channel);
        if (self::isRedisEnabled() === true) {
            $this->saveInCache($Channels->id, $Channel->userId, $Channels);
        }

        return $Channels;
    }

    public function delete(int $ChannelId, int $userId): string
    {
        $this->getChannelFromDb($ChannelId, $userId);
        $data = $this->getChannelRepository()->delete($ChannelId, $userId);
        if (self::isRedisEnabled() === true) {
            $this->deleteFromCache($ChannelId, $userId);
        }

        return $data;
    }
}
