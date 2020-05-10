<?php

declare(strict_types=1);

namespace App\Repository;

use App\Exception\Epg;

final class EpgRepository extends BaseRepository
{
    public function __construct(\PDO $database)
    {
        $this->database = $database;
    }

    public function checkAndGetEpg(int $EpgId, int $userId): object
    {
        $query = 'SELECT * FROM `Epgs` WHERE `id` = :id AND `userId` = :userId';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $EpgId);
        $statement->bindParam('userId', $userId);
        $statement->execute();
        $Epg = $statement->fetchObject();
        if (! $Epg) {
            throw new Epg('Epg not found.', 404);
        }

        return $Epg;
    }
    public function getTimeZone()
    {
        $query = 'SELECT `timezone` FROM `epg_data` ORDER BY `id`';
    }
    public function getAllEpgs(): array
    {
        $query = 'SELECT * FROM `Epgs` ORDER BY `id`';
        $statement = $this->getDb()->prepare($query);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getAll(int $userId): array
    {
        $query = 'SELECT * FROM `Epgs` WHERE `userId` = :userId ORDER BY `id`';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('userId', $userId);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getCatchup($input)
    {
        $query = 'SELECT `epg_data`.`id`, `epg_data`.`title`, `epg_data`.`short_description`, `epg_data`.`short_name`, `epg_data`.`duration_seconds`, `epg_data`.`program_start`, `epg_data`.`program_end`, `epg_data`.`long_description`, `channel`.`id` AS `channel.id`, `channel`.`title` AS `channel.title`, `channel`.`channel_number` AS `channel.channel_number`, `program_schedules`.`id` AS `program_schedules.id`, `channel_stream`.`stream_url` AS `channel.stream_url` 
                  FROM `epg_data` AS `epg_data` 
                  INNER JOIN `channels` AS `channel` ON `epg_data`.`channels_id` = `channel`.`id` AND `channel`.`id` = :channel_id AND `channel`.`company_id` = 1 
                  INNER JOIN `channel_stream` AS `channel_stream` ON `channel`.`id` = `channel_stream`.`channel_id` AND `channel_stream`.`stream_mode` ="catchup"
                  LEFT OUTER JOIN `program_schedule` AS `program_schedules` ON `epg_data`.`id` = `program_schedules`.`program_id` 
                  WHERE `epg_data`.`program_start` >= :date_s AND `epg_data`.`program_start` <= :date_e AND `epg_data`.`program_end` <= :date_now AND `epg_data`.`company_id` = 1 
                  ORDER BY `epg_data`.`program_start` ASC
                  ';
        $statement = $this->database->prepare($query);
        $y = date('Y')."-".$input['date'];
        $date_now =  date("Y-m-d H:i:s");
        //$timezone = $input['timezone'];
        $timezone = 7;
        $date_s = $y." 00:00:00";
        $date_s = date('Y-m-d H:i:s',strtotime($date_s."-".$timezone." hours"));
        $date_e = $y." 23:59:00";
        $date_e = date('Y-m-d H:i:s',strtotime($date_e."-".$timezone." hours"));
        //$date_e = "2020-05-1 23:59:00";
        $statement->bindParam('channel_id', $input['channel_id'], \PDO::PARAM_INT);
        $statement->bindParam('date_s', $date_s,\PDO::PARAM_STR,255);
        $statement->bindParam('date_e', $date_e,\PDO::PARAM_STR,255);
        $statement->bindParam('date_now', $date_now,\PDO::PARAM_STR,255);

        $statement->execute();
        $Epgs = $statement->fetchAll();
        return $Epgs;

    }

    public function getEpg($input)
    {
        $query = 'SELECT `epg_data`.`id`, `epg_data`.`title`, `epg_data`.`short_description`, `epg_data`.`short_name`, `epg_data`.`duration_seconds`, `epg_data`.`program_start`, `epg_data`.`program_end`, `epg_data`.`long_description`, `channel`.`id` AS `channel.id`, `channel`.`title` AS `channel.title`, `channel`.`channel_number` AS `channel.channel_number`, `program_schedules`.`id` AS `program_schedules.id`, `channel_stream`.`stream_url` AS `channel.stream_url` 
                  FROM `epg_data` AS `epg_data` 
                  INNER JOIN `channels` AS `channel` ON `epg_data`.`channels_id` = `channel`.`id` AND `channel`.`id` = :channel_id AND `channel`.`company_id` = 1 
                  INNER JOIN `channel_stream` AS `channel_stream` ON `channel`.`id` = `channel_stream`.`channel_id` AND `channel_stream`.`stream_mode` ="catchup"
                  LEFT OUTER JOIN `program_schedule` AS `program_schedules` ON `epg_data`.`id` = `program_schedules`.`program_id` 
                  WHERE `epg_data`.`program_start` >= :date_now AND `epg_data`.`program_start` >= :date_s AND `epg_data`.`program_start` <= :date_e  AND `epg_data`.`company_id` = 1 
                  ORDER BY `epg_data`.`program_start` ASC
                  ';
        $statement = $this->database->prepare($query);
        $y = date('Y')."-".$input['date'];
        $date_now =  date("Y-m-d H:i:s");
       // $date_now = '2020-04-03';
       // echo $date_now;
       // $timezone = $input['timezone'];
        $timezone = 7;
        $date_s = $y." 00:00:00";
        $date_s = date('Y-m-d H:i:s',strtotime($date_s."-".$timezone." hours"));
        $date_e = $y." 23:59:00";
        $date_e = date('Y-m-d H:i:s',strtotime($date_e."-".$timezone." hours"));
        //$date_e = "2020-05-1 23:59:00";
        $statement->bindParam('channel_id', $input['channel_id'], \PDO::PARAM_INT);
        $statement->bindParam('date_s', $date_s,\PDO::PARAM_STR,255);
        $statement->bindParam('date_e', $date_e,\PDO::PARAM_STR,255);
        $statement->bindParam('date_now', $date_now,\PDO::PARAM_STR,255);

        $statement->execute();
        $Epgs = $statement->fetchAll();
        return $Epgs;

    }
    public function search(string $EpgsName, int $userId, $status): array
    {
        $query = $this->getSearchEpgsQuery($status);
        $name = '%' . $EpgsName . '%';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('name', $name);
        $statement->bindParam('userId', $userId);
        if ($status === 0 || $status === 1) {
            $statement->bindParam('status', $status);
        }
        $statement->execute();

        return $statement->fetchAll();
    }

    public function create(object $Epg): object
    {
        $query = '
            INSERT INTO `Epgs` (`name`, `description`, `status`, `userId`)
            VALUES (:name, :description, :status, :userId)
        ';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('name', $Epg->name);
        $statement->bindParam('description', $Epg->description);
        $statement->bindParam('status', $Epg->status);
        $statement->bindParam('userId', $Epg->userId);
        $statement->execute();

        return $this->checkAndGetEpg((int) $this->database->lastInsertId(), (int) $Epg->userId);
    }

    public function update(object $Epg): object
    {
        $query = '
            UPDATE `Epgs`
            SET `name` = :name, `description` = :description, `status` = :status
            WHERE `id` = :id AND `userId` = :userId
        ';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $Epg->id);
        $statement->bindParam('name', $Epg->name);
        $statement->bindParam('description', $Epg->description);
        $statement->bindParam('status', $Epg->status);
        $statement->bindParam('userId', $Epg->userId);
        $statement->execute();

        return $this->checkAndGetEpg((int) $Epg->id, (int) $Epg->userId);
    }

    public function delete(int $EpgId, int $userId): string
    {
        $query = 'DELETE FROM `Epgs` WHERE `id` = :id AND `userId` = :userId';
        $statement = $this->getDb()->prepare($query);
        $statement->bindParam('id', $EpgId);
        $statement->bindParam('userId', $userId);
        $statement->execute();

        return 'The Epg was deleted.';
    }

    private function getSearchEpgsQuery($status)
    {
        $statusQuery = '';
        if ($status === 0 || $status === 1) {
            $statusQuery = 'AND `status` = :status';
        }

        return "
            SELECT * FROM `Epgs`
            WHERE `name` LIKE :name AND `userId` = :userId ${statusQuery}
            ORDER BY `id`
        ";
    }
}
