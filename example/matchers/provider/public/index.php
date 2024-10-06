<?php

use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../autoload.php';

$app = new FrameworkX\App();

$app->get('/matchers', function (ServerRequestInterface $request) {
    return Response::json([
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
        // 'likeDecimal' => 24, // Becareful, int is accepted
        'boolean' => true,
        'integer' => 11,
        'decimal' => 25.1, // int is not accepted
        'hexadecimal' => '20AC',
        'uuid' => 'e9d2f3a5-6ecc-4bff-8935-84bb6141325a',
        'ipv4Address' => '192.168.1.1',
        'ipv6Address' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
        'email' => 'pact@example.com',
        'nullValue' => null,
        'date' => '1997-12-11',
        'time' => '11:01:02',
        'datetime' => '1997-07-16T19:20:30',
        'likeString' => 'another string',
        'equal' => 'exact this value',
        'equalArray' => [
            'a',
            'bb',
            'ccc',
        ],
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
        'eachKey' => [
            'page 1' => 'Hello',
            'page 2' => 'World',
        ],
        'eachValue' => [
            'item 1' => 'bike',
            'item 2' => 'motorbike',
        ],
        'eachValueComplexValue' => [
            '35251397-d0d3-4178-af7d-4eb8ce7d8baa' => [
                'title' => 'Sintel',
                'year' => 2010,
                'length' => '14',
                'rating' => 7.4,
            ],
            '4ef94471-9ff5-476f-92f3-0bcf89166427' => [
                'title' => 'Tears of Steel',
                'year' => 2012,
                'length' => '12',
                'rating' => 5.5,
            ],
        ],
        'url' => 'https://www.example.com/users/1234/posts/latest',
        'matchAll' => [
            'tablet' => '300 usd',
            'laptop' => '1200 usd',
        ],
        'atLeast' => [
            null,
            null,
        ],
        'atMost' => [
            null,
        ],

        // Don't mind this. This is for demonstrating what query values provider will received.
        'query' => $request->getQueryParams(),
    ])
    ->withStatus(503)
    ->withHeader('X-Powered-By', [
        'PHP',
        'Nginx',
        'Slim',
    ]);
});

$app->post('/pact-change-state', function (ServerRequestInterface $request) {
    $body = json_decode((string) $request->getBody(), true);

    return Response::plaintext(sprintf('%s provider state %s with params: %s', $body['action'], $body['state'], json_encode($body['params'])));
});

$app->run();
