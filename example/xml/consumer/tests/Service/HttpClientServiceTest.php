<?php

namespace XmlConsumer\Tests\Service;

use Tienvx\PactPhpXml\Model\Options;
use Tienvx\PactPhpXml\XmlBuilder;
use XmlConsumer\Service\HttpClientService;
use PhpPact\Consumer\InteractionBuilder;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPact\Standalone\MockService\MockServerConfig;
use PHPUnit\Framework\TestCase;

class HttpClientServiceTest extends TestCase
{
    public function testGetMovies()
    {
        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/movies')
            ->addHeader('Accept', 'text/xml; charset=UTF8');

        $xmlBuilder = new XmlBuilder('1.0', 'UTF-8');
        $xmlBuilder
            ->start('movies')
                ->eachLike('movie')
                    ->start('title')
                        ->contentLike('PHP: Behind the Parser')
                    ->end()
                    ->start('characters')
                        ->eachLike('character', [], new Options(examples: 2))
                            ->start('name')
                                ->contentLike('Ms. Coder')
                            ->end()
                            ->start('actor')
                                ->contentLike('Onlivia Actora')
                            ->end()
                        ->end()
                    ->end()
                    ->start('plot')
                        ->contentLike(
                            <<<EOF
                            So, this language. It's like, a programming language. Or is it a
                            scripting language? All is revealed in this thrilling horror spoof
                            of a documentary.
                            EOF
                        )
                    ->end()
                    ->start('great-lines')
                        ->eachLike('line')
                            ->contentLike('PHP solves all my web problems')
                        ->end()
                    ->end()
                    ->start('rating', ['type' => 'thumbs'])
                        ->contentLike(7)
                    ->end()
                    ->start('rating', ['type' => 'stars'])
                        ->contentLike(5)
                    ->end()
                ->end()
            ->end();

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', 'text/xml')
            ->setBody(
                json_encode($xmlBuilder->getArray())
            );

        $config = new MockServerConfig();
        $config
            ->setConsumer('xmlConsumer')
            ->setProvider('xmlProvider')
            ->setPactDir(__DIR__.'/../../../pacts');
        if ($logLevel = \getenv('PACT_LOGLEVEL')) {
            $config->setLogLevel($logLevel);
        }
        $builder = new InteractionBuilder($config);
        $builder
            ->given('Movies exist')
            ->uponReceiving('A get request to /movies')
            ->with($request)
            ->willRespondWith($response);

        $service = new HttpClientService($config->getBaseUri());
        $moviesResult = new \SimpleXMLElement($service->getMovies());
        $verifyResult = $builder->verify();

        $this->assertTrue($verifyResult);
        $this->assertCount(1, $moviesResult);
    }
}
