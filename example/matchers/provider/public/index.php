<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../../../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->get('/matchers', function (Request $request, Response $response) {
    $response->getBody()->write(\json_encode([
        'like' => ['key' => 'another value'],
        'likeNull' => null,
        'eachLike' => ['item 1', 'item 2'],
        'atLeastLike' => [1, 2, 3, 4, 5, 6],
        'atMostLike' => [1, 2],
        'constrainedArrayLike' => ['item 1', 'item 2', 'item 3'],
        'regex' => '800 kilometers',
        'dateISO8601' => '2001-11-21',
        'timeISO8601' => 'T11:22:15.153Z',
        'dateTimeISO8601' => '2004-02-12T15:19:21+00:00',
        'dateTimeWithMillisISO8601' => '2018-11-07T00:25:00.073+01:00',
        'timestampRFC3339' => 'Thu, 01 Dec 1994 16:00:00 +0700',
        'likeBool' => false,
        'likeInt' => 34,
        'likeDecimal' => 24.12,
        'boolean' => true,
        'integer' => 11,
        'decimal' => 25.1,
        'hexadecimal' => '20AC',
        'uuid' => 'e9d2f3a5-6ecc-4bff-8935-84bb6141325a',
        'ipv4Address' => '192.168.1.1',
        'ipv6Address' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
        'email' => 'pact@example.com',
        'nullValue' => null,
        'date' => '1997-12-11',
        'time' => '11:01::02',
        'datetime' => '1997-07-16T19:20:30',
        'likeString' => 'another string',
        'equal' => 'exact this value',
        'includes' => 'The quick brown fox jumps over the lazy dog',
        'number' => 112.3,
        'arrayContaining' => [
            102.3,
            'eb375cad-48cc-4f7f-981b-ea4f1af90bf2',
        ],
        'notEmpty' => [111],
        'semver' => '0.27.1-beta2',
        'contentType' =>
            <<<HTML
            <!DOCTYPE html>
            <html>
            <body>

            <h1>My First Heading</h1>
            <p>My first paragraph.</p>

            </body>
            </html>
            HTML,
    ]));

    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/pact-change-state', function (Request $request, Response $response) {
    $body = $request->getParsedBody();

    printf('%s provider state %s with params: %s', $body['action'], $body['state'], json_encode($body['params']));

    return $response;
});

$app->run();
