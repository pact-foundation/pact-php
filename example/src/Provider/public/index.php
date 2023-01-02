<?php

use Provider\ExampleProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../../../vendor/autoload.php';

$app = AppFactory::create();

$provider = new ExampleProvider();

$app->get('/hello/{name}', function (Request $request, Response $response) use ($provider) {
    $name = $request->getAttribute('name');
    $response->getBody()->write(\json_encode(['message' => $provider->sayHello($name)]));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/goodbye/{name}', function (Request $request, Response $response) use ($provider) {
    $name = $request->getAttribute('name');
    $response->getBody()->write(\json_encode(['message' => $provider->sayGoodbye($name)]));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
