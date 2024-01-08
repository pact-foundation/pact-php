<?php

require __DIR__.'/../../../../vendor/autoload.php';

use Library\Name;
use Library\Person;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->post('/', function (Request $request, Response $response) {
    $body = $request->getParsedBody();
    if ($body['description'] === 'Person message sent') {
        $person = new Person();
        $person->setId('2d5554cd-22da-43ce-8842-2b42cf20661d');
        $name = new Name();
        $name->setGiven('Hettie');
        $name->setSurname('Toy');
        $person->setName($name);
        $response->getBody()->write($person->serializeToString());

        return $response
            ->withHeader('Content-Type', 'application/protobuf;message=Person')
            ->withHeader('Pact-Message-Metadata', \base64_encode(\json_encode([])));
    }

    $response->getBody()->write('Hello world!');

    return $response
        ->withHeader('Content-Type', 'text/plain')
    ;
});

$app->post('/pact-change-state', function (Request $request, Response $response) {
    $body = $request->getParsedBody();
    $response->getBody()->write(sprintf('State changed: %s', \json_encode([
        'action' => $body['action'],
        'state' => $body['state'],
        'params' => $body['params'],
    ])));

    return $response
        ->withHeader('Content-Type', 'text/plain')
    ;
});

$app->run();
