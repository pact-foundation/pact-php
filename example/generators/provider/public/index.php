<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../../../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->get('/generators', function (Request $request, Response $response) {
    $body = $request->getParsedBody();
    $response->getBody()->write(\json_encode([
        'regex' => '800 kilometers',
        'boolean' => true,
        'integer' => 11,
        'decimal' => 25.1,
        'hexadecimal' => '20AC',
        'uuid' => 'e9d2f3a5-6ecc-4bff-8935-84bb6141325a',
        'date' => '1997-12-11',
        'time' => '11:01::02',
        'datetime' => '1997-07-16T19:20:30',
        'string' => 'another string',
        'number' => 112.3,
        'requestId' => $body['id'],
    ]));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(400);
});

$app->post('/pact-change-state', function (Request $request, Response $response) {
    $response->getBody()->write(\json_encode([
        'id' => 222,
    ]));

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(200);
});

$app->run();
