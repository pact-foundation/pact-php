<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use GuzzleHttp\Client;

final class PactBroker implements PactBrokerInterface
{
    private Client $client;
    private string $consumer;
    private string $provider = 'p';

    public function __construct(private string $specificationVersion)
    {
        $this->client = new Client();
    }

    public function publish(int $id): void
    {
        $this->consumer = "c-$id";
        $this->client->put("http://localhost:9292/pacts/provider/$this->provider/consumer/$this->consumer/version/1.0.0", [
            'body' => file_get_contents(__DIR__."/../../pacts/$this->consumer-$this->provider.json"),
            'headers' => ['Content-Type' => 'application/json'],
        ]);
    }

    public function start(): void
    {
        exec('docker run --rm --publish 9292:9292 --detach --env PACT_BROKER_DATABASE_URL=sqlite:////tmp/pact_broker.sqlite3 --name pact-broker pactfoundation/pact-broker:latest');
        while (true) {
            try {
                $response = $this->client->get('http://localhost:9292/diagnostic/status/heartbeat', ['http_errors' => false]);
                if ($response->getStatusCode() !== 200) {
                    continue;
                }
                $status = json_decode($response->getBody(), true);
                if ($status['ok']) {
                    break;
                }
            } catch (\Throwable) {
            } finally {
                sleep(1);
            }
        }
    }

    public function stop(): void
    {
        exec('docker stop pact-broker');
        sleep(1);
    }

    public function getMatrix(): array
    {
        return json_decode(file_get_contents("http://localhost:9292/matrix.json?q[][pacticipant]=$this->consumer&q[][pacticipant]=$this->provider"), true);
    }
}
