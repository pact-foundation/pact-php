<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../../../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$path = __DIR__ . '/provider-states.json';
$get = fn (): array => json_decode(file_get_contents($path), true);
$set = fn (array $providerStates) => file_put_contents($path, json_encode($providerStates));

if (!file_exists($path)) {
    $set([]);
}

$stateChangeHandler = function (Request $request, Response $response) use ($get, $set) {
    $body = $request->getParsedBody();

    $providerStates = $get();
    $providerStates[] = $body;
    $set($providerStates);

    return $response;
};

$app->get('/has-action', function (Request $request, Response $response) use ($get) {
    $action = $request->getQueryParams()['action'];
    $hasAction = !empty(array_filter(
        $get(),
        fn (array $providerState) => $providerState['action'] === $action
    ));

    $response->getBody()->write((string) $hasAction);

    return $response->withHeader('Content-Type', 'text/plain');
});

$app->get('/has-state', function (Request $request, Response $response) use ($get) {
    $params = $request->getQueryParams();
    $action = $params['action'];
    $state = $params['state'];
    unset($params['action'], $params['state']);
    $hasState = !empty(array_filter(
        $get(),
        fn (array $providerState) =>
            $providerState['action'] === $action
            && $providerState['state'] === $state
            && $providerState['params'] == $params
        ));

    $response->getBody()->write((string) $hasState);

    return $response->withHeader('Content-Type', 'text/plain');
});

$app->post('/pact-change-state', $stateChangeHandler);

$app->post('/failed-pact-change-state', function (Request $request, Response $response) use ($stateChangeHandler) {
    $stateChangeHandler($request, $response);

    throw new \Exception('Cant do it');
});

$app->run();
