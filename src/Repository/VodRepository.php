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

    public function getAll(int $category_id): array
    {
        $query = " SELECT `vod`.`id`, `vod`.`vote_count`, `vod`.`vote_average`, `vod`.`title`, `vod`.`popularity`, \"film\" AS `vod_type`, `vod`.`trailer_url`, `vod`.`price`, `vod`.`expiration_time`,
                           concat('https://gm.tv1asia.com', `image_url`) AS `backdrop_path`, 
                           concat('https://gm.tv1asia.com', `vod`.`icon_url`) AS `poster_path`, `vod`.`original_language`, `vod`.`original_title`,
                            `vod`.`adult_content` AS `adult`, `vod`.`description` AS `overview`, DATE_FORMAT(`release_date`, '%Y-%m-%d') AS `release_date`, 
                            `t_vod_sales`.`id` AS `t_vod_sales.id`, `package_vods`.`id` AS `package_vods.id` 
                            FROM `vod` AS `vod` 
                            INNER JOIN `vod_vod_categories` AS `vod_vod_categories` 
                            ON `vod`.`id` = `vod_vod_categories`.`vod_id` AND `vod_vod_categories`.`category_id` = :category_id 
                            INNER JOIN `vod_category` AS `vod_vod_categories.vod_category` 
                            ON `vod_vod_categories`.`category_id` = `vod_vod_categories.vod_category`.`id` AND `vod_vod_categories.vod_category`.`password` = false 
                            LEFT OUTER JOIN `t_vod_sales` AS `t_vod_sales` 
                            ON `vod`.`id` = `t_vod_sales`.`vod_id` AND `t_vod_sales`.`end_time` >= '2020-04-15 08:14:38' 
                            LEFT OUTER JOIN `package_vod` AS `package_vods` 
                            ON `vod`.`id` = `package_vods`.`vod_id` AND `package_vods`.`package_id` IN (4) 
                            WHERE `vod`.`isavailable` = true AND `vod`.`company_id` = 1 AND `vod`.`expiration_time` >= '2020-04-15 08:14:38' AND `vod`.`pin_protected` = false AND `vod`.`adult_content` = false 
                            ORDER BY `vod`.`id`";
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('category_id', $category_id);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getByCat($vod): array
    {
        $query = "SELECT `vod`.`id`, `vod`.`vote_count`, `vod`.`vote_average`, `vod`.`title`, `vod`.`popularity`, \"film\" AS `vod_type`, `vod`.`trailer_url`, `vod`.`price`, `vod`.`expiration_time`, concat('https://gm.tv1asia.com', `image_url`) AS `backdrop_path`, concat('https://gm.tv1asia.com', `vod`.`icon_url`) AS `poster_path`, `vod`.`original_language`, `vod`.`original_title`, `vod`.`adult_content` AS `adult`, `vod`.`description` AS `overview`, DATE_FORMAT(`release_date`, '%Y-%m-%d') AS `release_date`, `t_vod_sales`.`id` AS `t_vod_sales.id` 
                  FROM `vod` AS `vod` 
                  INNER JOIN `vod_vod_categories` AS `vod_vod_categories` 
                  ON `vod`.`id` = `vod_vod_categories`.`vod_id` AND `vod_vod_categories`.`category_id` = :category_id 
                  INNER JOIN `vod_category` AS `vod_vod_categories.vod_category` 
                  ON `vod_vod_categories`.`category_id` = `vod_vod_categories.vod_category`.`id` AND `vod_vod_categories.vod_category`.`password` = false 
                  LEFT OUTER JOIN `t_vod_sales` AS `t_vod_sales` ON `vod`.`id` = `t_vod_sales`.`vod_id` AND `t_vod_sales`.`end_time` >= :now 
                  INNER JOIN `package_vod` AS `package_vod` ON `vod`.`id` = `package_vod`.`vod_id` 
                  INNER JOIN `package` AS `package_vod.package` ON `package_vod`.`package_id` = `package_vod.package`.`id` AND `package_vod.package`.`package_type_id` = 3
                  INNER JOIN `subscription` AS `package_vod.package.subscriptions` ON `package_vod.package`.`id` = `package_vod.package.subscriptions`.`package_id` AND `package_vod.package.subscriptions`.`login_id` = :login_id  
                  WHERE `vod`.`isavailable` = true AND `vod`.`company_id` = 1 AND `vod`.`expiration_time` >= :now1 AND `vod`.`pin_protected` = false AND `vod`.`adult_content` = false 
                  ORDER BY `vod`.`id` LIMIT :ofs, 50
                  ";
        $statement = $this->getDb()->prepare($query);
        $date_now =  date("Y-m-d H:i:s");
      //  $date_now = '2020-05-11';
        if (empty($vod['page'])){
            $vod['page'] = 1;
        }
        $ofs = ($vod['page'] - 1) * 50;
        $statement->bindParam('now', $date_now);
        $statement->bindParam('now1', $date_now);
        $statement->bindParam('category_id', $vod['category_id']);
        $statement->bindParam('login_id', $vod['login_data_id']);
        $statement->bindParam('ofs', $ofs);
        $statement->execute();
        $Vod = $statement->fetchAll();
        if (empty($Vod)) {
            throw new Vod('Vod not found.', 404);
        }
        return $Vod;
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
