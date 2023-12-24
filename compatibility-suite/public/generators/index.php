<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../../vendor/autoload.php';

$app = AppFactory::create();

$app->put('/request-generators', function (Request $request, Response $response) {
    file_put_contents(__DIR__ . '/body.json', $request->getBody()->getContents());
    file_put_contents(__DIR__ . '/headers.json', json_encode($request->getHeaders()));
    file_put_contents(__DIR__ . '/queryParams.json', json_encode($request->getQueryParams()));

    return $response;
});

$app->post('/return-provider-state-values', function (Request $request, Response $response) {
    $values = $request->getQueryParams();

    $response->getBody()->write(json_encode($values));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->any('{path:.*}', function ($request, $response, array $args) {
    file_put_contents(__DIR__ . '/path.txt', $args['path']);

    return $response;
});

$app->run();
