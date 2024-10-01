<?php

use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../autoload.php';

$app = new FrameworkX\App();

$app->get('/image.jpg', function (ServerRequestInterface $request) {
    return new Response(200, ['Content-Type' => 'image/jpeg'], file_get_contents(__DIR__.'/image.jpg'));
});

$app->run();
