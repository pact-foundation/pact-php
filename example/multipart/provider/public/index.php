<?php

use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../autoload.php';

$app = new FrameworkX\App();

$app->post('/user-profile', function (ServerRequestInterface $request) {
    $fileName = (string)$request->getUploadedFiles()['profile_image']->getClientFilename();

    return Response::json([
        'full_name' => (string)$request->getUploadedFiles()['full_name']->getStream(),
        'profile_image' => "http://example.test/$fileName",
        'personal_note' => (string)$request->getUploadedFiles()['personal_note']->getStream(),
    ]);
});

$app->run();
