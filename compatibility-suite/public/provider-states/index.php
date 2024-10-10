<?php

use React\Http\Message\Response;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../../../vendor/autoload.php';

$app = new FrameworkX\App();

$path = __DIR__ . '/provider-states.json';
$get = fn (): array => json_decode(file_get_contents($path), true);
$set = fn (array $providerStates) => file_put_contents($path, json_encode($providerStates));

if (!file_exists($path)) {
    $set([]);
}

$stateChangeHandler = function (ServerRequestInterface $request) use ($get, $set) {
    $body = json_decode((string) $request->getBody(), true);

    $providerStates = $get();
    $providerStates[] = $body;
    $set($providerStates);

    return new Response();
};

$app->get('/has-action', function (ServerRequestInterface $request) use ($get) {
    $action = $request->getQueryParams()['action'];
    $hasAction = !empty(array_filter(
        $get(),
        fn (array $providerState) => $providerState['action'] === $action
    ));

    return Response::plaintext((string) $hasAction);
});

$app->get('/has-state', function (ServerRequestInterface $request) use ($get) {
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

    return Response::plaintext((string) $hasState);
});

$app->post('/pact-change-state', $stateChangeHandler);

$app->post('/failed-pact-change-state', function (ServerRequestInterface $request) use ($stateChangeHandler): never {
    $stateChangeHandler($request);

    throw new \Exception('Cant do it');
});

$app->run();
