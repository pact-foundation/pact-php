<?php

use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../autoload.php';

$app = new FrameworkX\App();

$app->post('/pact-change-state', function (ServerRequestInterface $request): ResponseInterface {
    return Response::json([
        'created' => date('Y-m-d'),
        'id' => '3b46bf2c-fac7-47c3-8d0c-ff86f76c180c',
    ]);
});

$app->run();
