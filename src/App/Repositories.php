<?php

declare(strict_types=1);

use App\Repository\ChannelRepository;
use App\Repository\VodRepository;
use App\Repository\DeviceRepository;
use App\Repository\EpgRepository;
use App\Repository\LoginDataRepository;
use App\Repository\CustomerRepository;
use App\Repository\SerialRepository;
use App\Repository\VodCatRepository;

use App\Repository\NoteRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Psr\Container\ContainerInterface;

$container = $app->getContainer();

$container['user_repository'] = static function (ContainerInterface $container): UserRepository {
    return new UserRepository($container->get('db'));
};

$container['task_repository'] = static function (ContainerInterface $container): TaskRepository {
    return new TaskRepository($container->get('db'));
};

$container['note_repository'] = static function (ContainerInterface $container): NoteRepository {
    return new NoteRepository($container->get('db'));
};
$container['channel_repository'] = static function (ContainerInterface $container): ChannelRepository {
    return new ChannelRepository($container->get('db'));
};
$container['vod_repository'] = static function (ContainerInterface $container): VodRepository {
    return new VodRepository($container->get('db'));
};
$container['device_repository'] = static function (ContainerInterface $container): DeviceRepository {
    return new DeviceRepository($container->get('db'));
};
$container['loginData_repository'] = static function (ContainerInterface $container): LoginDataRepository {
    return new LoginDataRepository($container->get('db'));
};
$container['epg_repository'] = static function (ContainerInterface $container): EpgRepository {
    return new EpgRepository($container->get('db'));
};
$container['customer_repository'] = static function (ContainerInterface $container): CustomerRepository {
    return new CustomerRepository($container->get('db'));
};
$container['serial_repository'] = static function (ContainerInterface $container): SerialRepository {
    return new SerialRepository($container->get('db'));
};
$container['vodCat_repository'] = static function (ContainerInterface $container): VodCatRepository {
    return new VodCatRepository($container->get('db'));
};

