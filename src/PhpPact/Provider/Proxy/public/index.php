<?php

require __DIR__ . '/../../../../../vendor/autoload.php';

use Slim\Http\Request;
use Slim\Http\Response;

$app = new \Slim\App();

$app->post('/provider', function (Request $request, Response $response) {


    $body = (string) $request->getBody();
    $json = \json_decode($body);

    $class = $json->metadata->class;
    $method = $json->metadata->method;

    $content = \json_encode($json->content);

    return $response->withJson(['class' => "{$class}", 'method' => "{$method}"]);
});

$app->get('/health', function (Request $request, Response $response) {
    return $response->withJson(['status' => "OK"]);
});


$app->run();