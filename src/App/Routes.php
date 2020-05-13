<?php

declare(strict_types=1);

$app->get('/', 'App\Controller\DefaultController:getHelp');
$app->get('/status', 'App\Controller\DefaultController:getStatus');


$app->post('/login', \App\Controller\Customer\Login::class);
$app->post('/reg', \App\Controller\Customer\Create::class);
$app->post('/auth', \App\Controller\Device\Auth::class);

$app->group('/api/v1', function () use ($app): void {
    $app->group('/tasks', function () use ($app): void {
        $app->get('', \App\Controller\Task\GetAll::class);
        $app->get('/[{id}]', \App\Controller\Task\GetOne::class);
        $app->get('/search/[{query}]', \App\Controller\Task\Search::class);
        $app->post('', \App\Controller\Task\Create::class);
        $app->put('/[{id}]', \App\Controller\Task\Update::class);
        $app->delete('/[{id}]', \App\Controller\Task\Delete::class);
    })->add(new App\Middleware\Auth());

    /***Channel****/
    $app->group('/channel', function () use ($app): void {
        $app->get('', \App\Controller\Channel\GetAll::class);
        $app->get('/[{id}]', \App\Controller\Channel\GetOne::class);
        $app->get('/search/[{query}]', \App\Controller\Channel\Search::class);
        $app->post('', \App\Controller\Channel\Create::class);
        $app->put('/[{id}]', \App\Controller\Channel\Update::class);
        $app->delete('/[{id}]', \App\Controller\Channel\Delete::class);
    })->add(new App\Middleware\Auth());

    /***Vod****/
    $app->group('/vod', function () use ($app): void {
        $app->get('[/{category_id}[/{page}]]', \App\Controller\Vod\GetBycat::class);
       // $app->get('/[{id}]', \App\Controller\Vod\GetOne::class);
        $app->get('/search/[{query}]', \App\Controller\Vod\Search::class);
        $app->post('', \App\Controller\Vod\Create::class);
        $app->put('/[{id}]', \App\Controller\Vod\Update::class);
        $app->delete('/[{id}]', \App\Controller\Vod\Delete::class);
    })->add(new App\Middleware\Auth());

    /***VodCat****/
    $app->group('/vodcat', function () use ($app): void {
        $app->get('', \App\Controller\VodCat\GetAll::class);
        $app->get('/[{id}]', \App\Controller\VodCat\GetOne::class);
        $app->get('/search/[{query}]', \App\Controller\VodCat\Search::class);
        $app->post('', \App\Controller\VodCat\Create::class);
        $app->put('/[{id}]', \App\Controller\VodCat\Update::class);
        $app->delete('/[{id}]', \App\Controller\VodCat\Delete::class);
    })->add(new App\Middleware\Auth());

    /***CATCHUP****/
    $app->group('/catchup', function () use ($app): void {
        $app->get('[/{channel_id}[/{date}]]', \App\Controller\Epg\GetCatchup::class);
    })->add(new App\Middleware\Auth());
    $app->group('/epg', function () use ($app): void {
        $app->get('[/{channel_id}[/{date}]]', \App\Controller\Epg\GetEpg::class);
    })->add(new App\Middleware\Auth());

    /***LIST DATE****/

    $app->get('/getDateEpg', 'App\Controller\DefaultController:getDateEpg');
    $app->get('/getDateCatchup', 'App\Controller\DefaultController:getDateCatchup');

    /**** GET SUBSCRIPTION ****/

    $app->group('/subcriptions', function () use ($app) {
        $app->get('', \App\Controller\Subscri\GetAll::class);

    })->add(new App\Middleware\Auth());


    $app->group('/users', function () use ($app): void {
        $app->get('', \App\Controller\User\GetAll::class)->add(new App\Middleware\Auth());
        $app->get('/[{id}]', \App\Controller\User\GetOne::class)->add(new App\Middleware\Auth());
        $app->get('/search/[{query}]', \App\Controller\User\Search::class)->add(new App\Middleware\Auth());
        $app->post('', \App\Controller\User\Create::class);
        $app->put('/[{id}]', \App\Controller\User\Update::class)->add(new App\Middleware\Auth());
        $app->delete('/[{id}]', \App\Controller\User\Delete::class)->add(new App\Middleware\Auth());
    });

    $app->group('/notes', function () use ($app): void {
        $app->get('', \App\Controller\Note\GetAll::class);
        $app->get('/[{id}]', \App\Controller\Note\GetOne::class);
        $app->get('/search/[{query}]', \App\Controller\Note\Search::class);
        $app->post('', \App\Controller\Note\Create::class);
        $app->put('/[{id}]', \App\Controller\Note\Update::class);
        $app->delete('/[{id}]', \App\Controller\Note\Delete::class);
    });
});
