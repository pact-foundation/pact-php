<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../../../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->post('/users', function (Request $request, Response $response) {
    $auth = $request->getHeader('Authorization');
    if ($auth != 'Bearer 1a2b3c4d5e6f7g8h9i0k') {
        $response->withStatus(403);
    }

    error_log(sprintf('request body: %s', json_encode($request->getParsedBody())));

    $response->getBody()->write(\json_encode(['id' => '49dcfd3f-a5c9-49cb-a09e-a40a1da936b9']));

    return $response
        ->withStatus(201)
        ->withHeader('Content-Type', 'application/json');
});

$app->post('/pact-change-state', function (Request $request, Response $response) {
    return $response;
});

$app->run();
