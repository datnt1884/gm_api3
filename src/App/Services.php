<?php

declare(strict_types=1);

use App\Service\Channel\ChannelService;
use App\Service\Vod\VodService;
use App\Service\Device\DeviceService;
use App\Service\Customer\CustomerService;
use App\Service\LoginData\LoginDataService;
use App\Service\Serial\SerialService;
use App\Service\Epg\EpgService;
use App\Service\VodCat\VodCatService;
use App\Service\Subscri\SubscriService;



use App\Service\Note\NoteService;
use App\Service\Task\TaskService;
use App\Service\User\UserService;
use Psr\Container\ContainerInterface;

$container = $app->getContainer();

$container['user_service'] = static function (ContainerInterface $container): UserService {
    return new UserService($container->get('user_repository'), $container->get('redis_service'));
};

$container['task_service'] = static function (ContainerInterface $container): TaskService {
    return new TaskService($container->get('task_repository'), $container->get('redis_service'));
};

$container['note_service'] = static function (ContainerInterface $container): NoteService {
    return new NoteService($container->get('note_repository'), $container->get('redis_service'));
};
$container['channel_service'] = static function (ContainerInterface $container): ChannelService {
    return new ChannelService($container->get('channel_repository'), $container->get('redis_service'));
};
$container['vod_service'] = static function (ContainerInterface $container): VodService {
    return new VodService($container->get('vod_repository'), $container->get('redis_service'));
};
$container['device_service'] = static function (ContainerInterface $container): DeviceService {
    return new DeviceService($container->get('device_repository'), $container->get('redis_service'));
};
$container['customer_service'] = static function (ContainerInterface $container): CustomerService {
    return new CustomerService($container->get('customer_repository'), $container->get('redis_service'));
};
$container['loginData_service'] = static function (ContainerInterface $container): LoginDataService {
    return new LoginDataService($container->get('loginData_repository'), $container->get('redis_service'));
};
$container['serial_service'] = static function (ContainerInterface $container): SerialService {
    return new SerialService($container->get('serial_repository'), $container->get('redis_service'));
};
$container['epg_service'] = static function (ContainerInterface $container): EpgService {
    return new EpgService($container->get('epg_repository'), $container->get('redis_service'));
};
$container['vodCat_service'] = static function (ContainerInterface $container): VodCatService {
    return new VodCatService($container->get('vodCat_repository'), $container->get('redis_service'));
};
$container['subscri_service'] = static function (ContainerInterface $container): SubscriService {
    return new SubscriService($container->get('subscri_repository'), $container->get('redis_service'));
};