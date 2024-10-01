<?php

use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../../../vendor/autoload.php';

$app = new FrameworkX\App();

$app->put('/request-generators', function (ServerRequestInterface $request) {
    file_put_contents(__DIR__ . '/body.json', $request->getBody()->getContents());
    file_put_contents(__DIR__ . '/headers.json', json_encode($request->getHeaders()));
    file_put_contents(__DIR__ . '/queryParams.json', json_encode($request->getQueryParams()));

    return new Response();
});

$app->post('/return-provider-state-values', function (ServerRequestInterface $request) {
    $values = $request->getQueryParams();

    return Response::json($values);
});

$app->any('{path:.*}', function (ServerRequestInterface $request) {
    file_put_contents(__DIR__ . '/path.txt', $request->getAttribute('path'));

    return new Response();
});

$app->run();
