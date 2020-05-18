<?php

declare(strict_types=1);

namespace App\Repository;
use App\Exception\Channel;

abstract class BaseRepository
{
    protected $database;

    protected function getDb(): \PDO
    {
        return $this->database;
    }
    public function getDeviceId(int $userId): array
    {
        $query = "SELECT t2.`login_data_id`, t2.`device_id`, t1.`customer_id` , t2. `username`
                  FROM `login_data` t1 
                  INNER JOIN `devices` t2  ON t1.id = t2.login_data_id 
                  WHERE t2.`id` = :userId
                  ";
        $statement = $this->getDb()->prepare($query);
        // $statement->bindParam('id', $ChannelId);
        // $userId = '4187998b218a1641';
        $statement->bindParam('userId', $userId);
        $statement->execute();
        $Channel = $statement->fetchAll();
        if (empty($Channel)) {
            throw new Channel('Channel not found.', 404);
        }

        return $Channel;
    }
}
