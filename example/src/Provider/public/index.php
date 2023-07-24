<?php

use Provider\ExampleProvider;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../../../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

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

$app->post('/pact-messages', function (Request $request, Response $response) use ($provider) {
    $body = $request->getParsedBody();
    $message = $provider->dispatchMessage($body['description'], $body['providerStates']);
    if ($message) {
        $response->getBody()->write(\json_encode($message->getContents()));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Pact-Message-Metadata', \base64_encode(\json_encode($message->getMetadata())));
    }

    return $response;
});

$app->post('/pact-change-state', function (Request $request, Response $response) use ($provider) {
    $body = $request->getParsedBody();
    $provider->changeSate($body['action'], $body['state'], $body['params']);

    return $response;
});

$app->run();
