<?php

use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../autoload.php';

$app = new FrameworkX\App();

$app->get('/generators', function (ServerRequestInterface $request) {
    $body = json_decode((string) $request->getBody(), true);

    return Response::json([
        'regex' => '800 kilometers',
        'boolean' => true,
        'integer' => 11,
        'decimal' => 25.1,
        'hexadecimal' => '20AC',
        'uuid' => 'e9d2f3a5-6ecc-4bff-8935-84bb6141325a',
        'date' => '1997-12-11',
        'time' => '11:01:02',
        'datetime' => '1997-07-16T19:20:30',
        'string' => 'another string',
        'number' => 112.3,
        'url' => 'https://www.example.com/users/1234/posts/latest',
        'requestId' => $body['id'],
    ])
    ->withStatus(400);
});

$app->post('/pact-change-state', function (ServerRequestInterface $request) {
    return Response::json([
        'id' => 222,
    ]);
});

$app->run();
