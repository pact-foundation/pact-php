<?php

use GuzzleHttp\Psr7\Uri;
use PhpPact\Broker\Service\BrokerHttpClient;
use PhpPact\Http\GuzzleClient;

require_once __DIR__ . '/../../../vendor/autoload.php';

$httpService = new BrokerHttpClient(new GuzzleClient(), new Uri('http://localhost:8080/'));

$json = \json_encode([
    'consumer' => 'someConsumer',
    'provider' => 'someProvider'
]);

$httpService->publishJson('1.0.0', $json);
