<?php

require __DIR__.'/../autoload.php';

use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../autoload.php';

$app = new FrameworkX\App();

$app->get('/report.csv', function (ServerRequestInterface $request) {
    return new Response(200, ['Content-Type' => 'text/csv;charset=utf-8'], file_get_contents(__DIR__.'/report.csv'));
});

$app->post('/pact-change-state', function (ServerRequestInterface $request) {
    return new Response();
});

$app->run();
