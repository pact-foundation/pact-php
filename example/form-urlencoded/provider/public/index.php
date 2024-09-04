<?php

use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../../../../vendor/autoload.php';

$app = new FrameworkX\App();

$app->post('/users', function (ServerRequestInterface $request) {
    $response = new Response();
    $auth = $request->getHeaderLine('Authorization');
    if ($auth != 'Bearer 1a2b3c4d5e6f7g8h9i0k') {
        return $response->withStatus(403);
    }

    error_log(sprintf('request body: %s', (string) $request->getBody()));

    $response->getBody()->write(\json_encode(['id' => '49dcfd3f-a5c9-49cb-a09e-a40a1da936b9']));

    return $response
        ->withStatus(201)
        ->withHeader('Content-Type', 'application/json');
});

$app->post('/pact-change-state', function (ServerRequestInterface $request) {
    return new Response();
});

$app->run();
