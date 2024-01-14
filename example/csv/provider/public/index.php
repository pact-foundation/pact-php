<?php

require __DIR__.'/../../../../vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();

$app->post('/pact-change-state', function (Request $request, Response $response) {
    return $response;
});

$app->run();
