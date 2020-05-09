<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\Channel;

class ChannelRepository extends BaseRepository
{
    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }

    public function checkAndGetChannel(int $ChannelId, int $userId)
    {
        $query = 'SELECT * FROM `channels` WHERE `id` = :id AND `userId` = :userId';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $ChannelId);
        $statement->bindParam('userId', $userId);
        $statement->execute();
        $Channel = $statement->fetchObject();
        if (empty($Channel)) {
            throw new Channel('Channel not found----.', 404);
        }

        return $Channel;
    }


    public function getAllChannels(): array
    {
        $query = "SELECT `channels`.`id`, `channels`.`genre_id`, `channels`.`channel_number`, `channels`.`title`, `channels`.`icon_url`, `channels`.`pin_protected`, `channel_streams`.`stream_source_id` AS `channel_streams.stream_source_id`, `channel_streams`.`stream_url` AS `channel_streams.stream_url`, `channel_streams`.`stream_format` AS `channel_streams.stream_format`, `channel_streams`.`token` AS `channel_streams.token`, `channel_streams`.`token_url` AS `channel_streams.token_url`, `channel_streams`.`is_octoshape` AS `channel_streams.is_octoshape`, `channel_streams`.`drm_platform` AS `channel_streams.drm_platform`, `channel_streams`.`encryption` AS `channel_streams.encryption`, `channel_streams`.`encryption_url` AS `channel_streams.encryption_url`, `favorite_channels`.`id` AS `favorite_channels.id` 
                  FROM `channels` AS `channels` 
                  INNER JOIN `channel_stream` AS `channel_streams` 
                  ON `channels`.`id` = `channel_streams`.`channel_id` AND `channel_streams`.`stream_source_id` = 1 AND `channel_streams`.`stream_mode` = 'live' AND `channel_streams`.`stream_resolution` LIKE '%1%' 
                  INNER JOIN `genre` AS `genre` ON `channels`.`genre_id` = `genre`.`id` AND `genre`.`is_available` = true 
                  INNER JOIN `packages_channels` AS `packages_channels` ON `channels`.`id` = `packages_channels`.`channel_id` 
                  INNER JOIN `package` AS `packages_channels.package` ON `packages_channels`.`package_id` = `packages_channels.package`.`id` AND `packages_channels.package`.`package_type_id` = 1 
                  INNER JOIN `subscription` AS `packages_channels.package.subscriptions` ON `packages_channels.package`.`id` = `packages_channels.package.subscriptions`.`package_id` AND `packages_channels.package.subscriptions`.`login_id` = 11  
                  LEFT OUTER JOIN `favorite_channels` AS `favorite_channels` ON `channels`.`id` = `favorite_channels`.`channel_id` AND `favorite_channels`.`user_id` = 1 WHERE `channels`.`isavailable` = 1 AND `channels`.`pin_protected` = 0 AND `channels`.`company_id` = 1 
                  GROUP BY `id` ORDER BY `channels`.`channel_number` ASC";
        $statement = $this->getDb()->prepare($query);

        $statement->execute();

        return $statement->fetchAll();
    }
    public function getCatChannels(int $userId, int $genreId): array
    {
        $query =" SELECT `channels`.`id`, `channels`.`genre_id`, `channels`.`channel_number`, `channels`.`title`, `channels`.`icon_url`, `channels`.`pin_protected`, `channel_streams`.`stream_source_id` AS `channel_streams.stream_source_id`, `channel_streams`.`stream_url` AS `channel_streams.stream_url`, `channel_streams`.`stream_format` AS `channel_streams.stream_format`, `channel_streams`.`token` AS `channel_streams.token`, `channel_streams`.`token_url` AS `channel_streams.token_url`, `channel_streams`.`is_octoshape` AS `channel_streams.is_octoshape`, `channel_streams`.`drm_platform` AS `channel_streams.drm_platform`, `channel_streams`.`encryption` AS `channel_streams.encryption`, `channel_streams`.`encryption_url` AS `channel_streams.encryption_url`, `favorite_channels`.`id` AS `favorite_channels.id` FROM `channels` AS `channels` INNER JOIN `channel_stream` AS `channel_streams` ON `channels`.`id` = `channel_streams`.`channel_id` AND `channel_streams`.`stream_source_id` = 1 AND `channel_streams`.`stream_mode` = 'live' AND `channel_streams`.`stream_resolution` LIKE '%2%' INNER JOIN `genre` AS `genre` ON `channels`.`genre_id` = `genre`.`id` AND `genre`.`is_available` = true INNER JOIN `packages_channels` AS `packages_channels` ON `channels`.`id` = `packages_channels`.`channel_id` INNER JOIN `package` AS `packages_channels.package` ON `packages_channels`.`package_id` = `packages_channels.package`.`id` AND `packages_channels.package`.`package_type_id` = 2 INNER JOIN `subscription` AS `packages_channels.package.subscriptions` ON `packages_channels.package`.`id` = `packages_channels.package.subscriptions`.`package_id` AND `packages_channels.package.subscriptions`.`login_id` = :login_id  LEFT OUTER JOIN `favorite_channels` AS `favorite_channels` ON `channels`.`id` = `favorite_channels`.`channel_id` AND `favorite_channels`.`user_id` = :user_id WHERE `channels`.`isavailable` = 1 AND `channels`.`pin_protected` = 0 AND `channels`.`company_id` = 1 AND `channels`.`genre_id` = :genre_id GROUP BY `id` ORDER BY `channels`.`channel_number` ASC";
        $statement = $this->getDb()->prepare($query);
        //$userId ="125";
        $statement->bindParam('user_id', $userId);
        $statement->bindParam('login_id', $userId);
        $statement->bindParam('genre_id', $genreId);

        $statement->execute();

        return $statement->fetchAll();
    }
    public function getByGenre(int $userId, int $genre_id){
        $query = "SELECT `channels`.`id`, `channels`.`genre_id`, `channels`.`channel_number`, `channels`.`title`, `channels`.`icon_url`, `channels`.`pin_protected`, `channel_streams`.`stream_source_id` AS `channel_streams.stream_source_id`, `channel_streams`.`stream_url` AS `channel_streams.stream_url`, `channel_streams`.`stream_format` AS `channel_streams.stream_format`, `channel_streams`.`token` AS `channel_streams.token`, `channel_streams`.`token_url` AS `channel_streams.token_url`, `channel_streams`.`is_octoshape` AS `channel_streams.is_octoshape`, `channel_streams`.`drm_platform` AS `channel_streams.drm_platform`, `channel_streams`.`encryption` AS `channel_streams.encryption`, `channel_streams`.`encryption_url` AS `channel_streams.encryption_url`, `favorite_channels`.`id` AS `favorite_channels.id` 
                  FROM `channels` AS `channels` 
                  INNER JOIN `channel_stream` AS `channel_streams` ON `channels`.`id` = `channel_streams`.`channel_id` AND `channel_streams`.`stream_source_id` = 1 AND `channel_streams`.`stream_mode` = 'live' AND `channel_streams`.`stream_resolution` LIKE '%2%' 
                  INNER JOIN `genre` AS `genre` ON `channels`.`genre_id` = `genre`.`id` AND `genre`.`is_available` = true 
                  INNER JOIN `packages_channels` AS `packages_channels` ON `channels`.`id` = `packages_channels`.`channel_id` 
                  INNER JOIN `package` AS `packages_channels.package` ON `packages_channels`.`package_id` = `packages_channels.package`.`id` AND `packages_channels.package`.`package_type_id` = 2 
                  INNER JOIN `subscription` AS `packages_channels.package.subscriptions` ON `packages_channels.package`.`id` = `packages_channels.package.subscriptions`.`package_id` AND `packages_channels.package.subscriptions`.`login_id` = :login_id  
                  LEFT OUTER JOIN `favorite_channels` AS `favorite_channels` ON `channels`.`id` = `favorite_channels`.`channel_id` AND `favorite_channels`.`user_id` = :user_id 
                  WHERE `channels`.`genre_id` = :genre_id AND `channels`.`isavailable` = 1 AND `channels`.`pin_protected` = 0 AND `channels`.`company_id` = 1 
                  GROUP BY `id` 
                  ORDER BY `channels`.`channel_number` ASC
                  ";

    }
    public function getAll(int $userId): array
    {
        $query =" SELECT `channels`.`id`, `channels`.`genre_id`, `channels`.`channel_number`, `channels`.`title`, `channels`.`icon_url`, `channels`.`pin_protected`, `channel_streams`.`stream_source_id` AS `channel_streams.stream_source_id`, `channel_streams`.`stream_url` AS `channel_streams.stream_url`, `channel_streams`.`stream_format` AS `channel_streams.stream_format`, `channel_streams`.`token` AS `channel_streams.token`, `channel_streams`.`token_url` AS `channel_streams.token_url`, `channel_streams`.`is_octoshape` AS `channel_streams.is_octoshape`, `channel_streams`.`drm_platform` AS `channel_streams.drm_platform`, `channel_streams`.`encryption` AS `channel_streams.encryption`, `channel_streams`.`encryption_url` AS `channel_streams.encryption_url`, `favorite_channels`.`id` AS `favorite_channels.id` 
                  FROM `channels` AS `channels` 
                  INNER JOIN `channel_stream` AS `channel_streams` 
                  ON `channels`.`id` = `channel_streams`.`channel_id` AND `channel_streams`.`stream_source_id` = 1 AND `channel_streams`.`stream_mode` = 'live' AND `channel_streams`.`stream_resolution` LIKE '%2%' 
                  INNER JOIN `genre` AS `genre` ON `channels`.`genre_id` = `genre`.`id` AND `genre`.`is_available` = true 
                  INNER JOIN `packages_channels` AS `packages_channels` ON `channels`.`id` = `packages_channels`.`channel_id` 
                  INNER JOIN `package` AS `packages_channels.package` ON `packages_channels`.`package_id` = `packages_channels.package`.`id` AND `packages_channels.package`.`package_type_id` = 2 
                  INNER JOIN `subscription` AS `packages_channels.package.subscriptions` ON `packages_channels.package`.`id` = `packages_channels.package.subscriptions`.`package_id` AND `packages_channels.package.subscriptions`.`login_id` = :login_id  
                  LEFT OUTER JOIN `favorite_channels` AS `favorite_channels` ON `channels`.`id` = `favorite_channels`.`channel_id` AND `favorite_channels`.`user_id` = :user_id 
                  WHERE `channels`.`isavailable` = 1 AND `channels`.`pin_protected` = 0 AND `channels`.`company_id` = 1 
                  GROUP BY `id` ORDER BY `channels`.`channel_number` ASC";
        $statement = $this->getDb()->prepare($query);
         $userId ="120";
        echo $userId;
        $statement->bindParam('user_id', intval($userId,10), \PDO::PARAM_INT);
        $statement->bindParam('login_id', intval($userId,10), \PDO::PARAM_INT);

        $statement->execute();

        $Channel = $statement->fetchAll();
        if (empty($Channel)) {
            throw new Channel('Channel not found----.', 404);
        }
        return $Channel;

    }

    public function search($ChannelsName, int $userId, $status): array
    {
        $query = $this->getSearchChannelsQuery($status);
        $name = '%' . $ChannelsName . '%';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('name', $name);
        $statement->bindParam('userId', $userId);
        if ($status === 0 || $status === 1) {
            $statement->bindParam('status', $status);
        }
        $statement->execute();

        return $statement->fetchAll();
    }

    private function getSearchChannelsQuery($status)
    {
        $statusQuery = '';
        if ($status === 0 || $status === 1) {
            $statusQuery = 'AND `status` = :status';
        }

        return "
            SELECT * FROM `Channels`
            WHERE `name` LIKE :name AND `userId` = :userId $statusQuery
            ORDER BY `id`
        ";
    }

    public function create($Channel)
    {
        $query = '
            INSERT INTO `Channels` (`name`, `description`, `status`, `userId`)
            VALUES (:name, :description, :status, :userId)
        ';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('name', $Channel->name);
        $statement->bindParam('description', $Channel->description);
        $statement->bindParam('status', $Channel->status);
        $statement->bindParam('userId', $Channel->userId);
        $statement->execute();

        return $this->checkAndGetChannel((int) $this->database->lastInsertId(), (int) $Channel->userId);
    }

    public function update($Channel)
    {
        $query = '
            UPDATE `Channels`
            SET `name` = :name, `description` = :description, `status` = :status
            WHERE `id` = :id AND `userId` = :userId
        ';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $Channel->id);
        $statement->bindParam('name', $Channel->name);
        $statement->bindParam('description', $Channel->description);
        $statement->bindParam('status', $Channel->status);
        $statement->bindParam('userId', $Channel->userId);
        $statement->execute();

        return $this->checkAndGetChannel((int) $Channel->id, (int) $Channel->userId);
    }

    public function delete(int $ChannelId, int $userId): string
    {
        $query = 'DELETE FROM `Channels` WHERE `id` = :id AND `userId` = :userId';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $ChannelId);
        $statement->bindParam('userId', $userId);
        $statement->execute();

        return 'The Channel was deleted.';
    }
}
