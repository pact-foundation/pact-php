<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use GuzzleHttp\Client;
use PhpPactTest\CompatibilitySuite\Model\PactPath;

final class PactBroker implements PactBrokerInterface
{
    private Client $client;
    private PactPath $pactPath;

    public function __construct(private string $specificationVersion)
    {
        $this->client = new Client();
    }

    public function publish(int $id): void
    {
        $this->pactPath = new PactPath("c-$id");
        $this->client->put("http://localhost:9292/pacts/provider/{$this->pactPath->getProvider()}/consumer/{$this->pactPath->getConsumer()}/version/1.0.0", [
            'body' => file_get_contents($this->pactPath),
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
        return json_decode(file_get_contents("http://localhost:9292/matrix.json?q[][pacticipant]={$this->pactPath->getConsumer()}&q[][pacticipant]={$this->pactPath->getProvider()}"), true);
    }
}
