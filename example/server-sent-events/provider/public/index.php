<?php

require __DIR__.'/../autoload.php';

use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

$app = new FrameworkX\App();

$app->get('/events', function (ServerRequestInterface $request) {
    $sseData = "id:901
retry:20

data: I am

event:user
data: id: 12, name: John

data: I have many books

event:count
data:12

data: I love this book.
data: I read it many times

event:count
data:34

id: 902

data: Last time I read it

retry: 210

event:time
data:2015-02-21

";
    return new Response(200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
        'X-Accel-Buffering' => 'no',
    ], $sseData);
});

$app->post('/pact-change-state', function (ServerRequestInterface $request) {
    return new Response();
});

$app->run();
