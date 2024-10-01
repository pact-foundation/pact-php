<?php

require __DIR__.'/../autoload.php';

use Library\Name;
use Library\Person;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

$app = new FrameworkX\App();

$app->post('/', function (ServerRequestInterface $request) {
    $body = json_decode((string) $request->getBody(), true);
    if ($body['description'] === 'Person message sent') {
        $person = new Person();
        $person->setId('2d5554cd-22da-43ce-8842-2b42cf20661d');
        $name = new Name();
        $name->setGiven('Hettie');
        $name->setSurname('Toy');
        $person->setName($name);

        $response = new Response(200);
        $response->getBody()->write($person->serializeToString());

        return $response
            ->withHeader('Content-Type', 'application/protobuf;message=.library.Person')
            ->withHeader('Pact-Message-Metadata', \base64_encode(\json_encode([])));
    }

    return Response::plaintext('Hello world!');
});

$app->post('/pact-change-state', function (ServerRequestInterface $request) {
    $body = json_decode((string) $request->getBody(), true);

    return Response::plaintext(sprintf('State changed: %s', \json_encode([
        'action' => $body['action'],
        'state' => $body['state'],
        'params' => $body['params'],
    ])));
});

$app->run();
