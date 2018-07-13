<?php

require __DIR__ . '/../../../../../vendor/autoload.php';

use Slim\Http\Request;
use Slim\Http\Response;

$app = new \Slim\App();

// default base url for pact-provider-verifier.bat
// ex: pact-provider-verifier.bat D:\Temp\test_consumer-test_provider.json --provider-base-url=http://localhost:7201 --custom-provider-header='Callback: MyClass,MyHeader'
$app->post('/', function (Request $request, Response $response) {
    $headerCallback = $request->getHeader('HTTP_CALLBACK');
    $arrayCallback = \explode(',', $headerCallback[0]);

    $class = $arrayCallback[0];
    $method = $arrayCallback[1];
    \error_log("{$class}::{$method}");

    return $response->withJson(['status' => 'OK']);
});

$app->get('/health', function (Request $request, Response $response) {
    return $response->withJson(['status' => 'OK']);
});

$app->run();
