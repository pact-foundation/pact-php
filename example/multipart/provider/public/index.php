<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../../../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->post('/user-profile', function (Request $request, Response $response) {
    $fileName = (string)$request->getUploadedFiles()['profile_image']->getClientFilename();
    $response->getBody()->write(\json_encode([
        'full_name' => (string)$request->getUploadedFiles()['full_name']->getStream(),
        'profile_image' => "http://example.test/$fileName",
        'personal_note' => (string)$request->getUploadedFiles()['personal_note']->getStream(),
    ]));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
