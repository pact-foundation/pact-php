<?php

use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../autoload.php';

$app = new FrameworkX\App();

$app->post('/user-profile', function (ServerRequestInterface $request) {
    /** @var array<string, \Psr\Http\Message\UploadedFileInterface> $uploadedFiles */
    $uploadedFiles = $request->getUploadedFiles();
    $fileName = (string) $uploadedFiles['profile_image']->getClientFilename();

    return Response::json([
        'full_name' => (string) $uploadedFiles['full_name']->getStream(),
        'profile_image' => "http://example.test/$fileName",
        'personal_note' => (string) $uploadedFiles['personal_note']->getStream(),
    ]);
});

$app->run();
