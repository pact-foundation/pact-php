<?php

use MessageProvider\ExampleProvider;
use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../autoload.php';

$app = new FrameworkX\App();

$provider = new ExampleProvider();

$app->post('/pact-messages', function (ServerRequestInterface $request) use ($provider) {
    /** @var array{description: string, providerStates: array<array-key, mixed>} $body */
    $body = json_decode((string) $request->getBody(), true);
    $message = $provider->dispatchMessage($body['description'], $body['providerStates']);
    $response = new Response();
    if ($message) {
        $response->getBody()->write((string) \json_encode($message->getContents()));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Pact-Message-Metadata', \base64_encode((string) \json_encode($message->getMetadata())));
    }

    return $response;
});

$app->post('/pact-change-state', function (ServerRequestInterface $request) use ($provider) {
    /** @var array{action: string, state: string, params: array<array-key, mixed>} $body */
    $body = json_decode((string) $request->getBody(), true);
    $provider->changeState($body['action'], $body['state'], $body['params']);
    $currentState = $provider->getCurrentState();

    return Response::plaintext(sprintf('%s provider state %s', $currentState['action'] ?? '', $currentState['state'] ?? ''));
});

$app->run();
