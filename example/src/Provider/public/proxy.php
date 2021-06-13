<?php

require __DIR__ . '/../../../../vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->post('/', function (Request $request, Response $response) {
    $body = $request->getParsedBody();
    switch ($body['description']) {
        case 'an alligator named Mary exists':
            $response->getBody()->write(\json_encode([
                'text' => 'Hello Mary',
            ]));

            return $response->withHeader('Content-Type', 'application/json')
                ->withHeader('pact_message_metadata', \base64_encode(\json_encode([
                    'queue'       => 'wind cries',
                    'routing_key' => 'wind cries',
                ])));
        case 'footprints dressed in red':
            $response->getBody()->write(\json_encode([
                'song' => 'And the wind whispers Mary',
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withHeader('pact_message_metadata', \base64_encode(\json_encode([
                    'queue'       => 'And the clowns have all gone to bed',
                    'routing_key' => 'And the clowns have all gone to bed',
                ])));

        default:
            break;
    }
    // What to do with $body['providerStates'] ?

    return $response;
});

$app->post('/change-state', function (Request $request, Response $response) {
    $body = $request->getParsedBody();
    switch ($body['state']) {
        case 'a message':
        case 'You can hear happiness staggering on down the street':
            if (($body['action'] ?? null) === 'teardown') {
                \error_log('Removing fixtures...');
            } else {
                \error_log('Creating fixtures...');
            }

            break;

        default:
            break;
    }

    return $response;
});

try {
    $app->run();
} catch (HttpNotFoundException $exception) {
    return false;
}
