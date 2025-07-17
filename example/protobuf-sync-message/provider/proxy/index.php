<?php

use Psr\Http\Message\ResponseInterface;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../autoload.php';

$app = new FrameworkX\App();

$app->post('/pact-change-state', function (ServerRequestInterface $request): ResponseInterface {
    return Response::json([
        'created' => date('Y-m-d'),
    ]);
});

$app->run();
