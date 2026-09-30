<?php

use JsonProvider\ExampleProvider;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../autoload.php';

$app = new FrameworkX\App();

$provider = new ExampleProvider();

$app->get('/hello/{name}', function (ServerRequestInterface $request) use ($provider) {
    /** @var string $name */
    $name = $request->getAttribute('name');

    return Response::json(['message' => $provider->sayHello($name)]);
});

$app->get('/goodbye/{name}', function (ServerRequestInterface $request) use ($provider) {
    /** @var string $name */
    $name = $request->getAttribute('name');

    return Response::json(['message' => $provider->sayGoodbye($name)]);
});

$app->post('/pact-change-state', function (ServerRequestInterface $request) use ($provider) {
    /** @var array{action: string, state: string, params: array<array-key, mixed>} $body */
    $body = json_decode((string) $request->getBody(), true);
    $provider->changeState($body['action'], $body['state'], $body['params']);
    $currentState = $provider->getCurrentState();

    return Response::plaintext(sprintf('%s provider state %s', $currentState['action'] ?? '', $currentState['state'] ?? ''));
});

$app->run();
