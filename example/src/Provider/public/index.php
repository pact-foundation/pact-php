<?php

use Slim\Http\Request;
use Slim\Http\Response;

require __DIR__ . '/../../../../vendor/autoload.php';

$app = new \Slim\App();

$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');

    return $response->withJson(['message' => "Hello, {$name}"]);
});

$app->get('/goodbye/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');

    return $response->withJson(['message' => "Goodbye, {$name}"]);
});


$app->run();
