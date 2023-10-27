<?php

namespace XmlConsumer\Tests\Service;

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
            ->root(
                $xmlBuilder->name('movies'),
                $xmlBuilder->add(
                    $xmlBuilder->eachLike(),
                    $xmlBuilder->name('movie'),
                    $xmlBuilder->add(
                        $xmlBuilder->name('title'),
                        $xmlBuilder->text(
                            $xmlBuilder->contentLike('PHP: Behind the Parser'),
                        ),
                    ),
                    $xmlBuilder->add(
                        $xmlBuilder->name('characters'),
                        $xmlBuilder->add(
                            $xmlBuilder->eachLike(examples: 2),
                            $xmlBuilder->name('character'),
                            $xmlBuilder->add(
                                $xmlBuilder->name('name'),
                                $xmlBuilder->text(
                                    $xmlBuilder->contentLike('Ms. Coder'),
                                ),
                            ),
                            $xmlBuilder->add(
                                $xmlBuilder->name('actor'),
                                $xmlBuilder->text(
                                    $xmlBuilder->contentLike('Onlivia Actora'),
                                ),
                            ),
                        ),
                    ),
                    $xmlBuilder->add(
                        $xmlBuilder->name('plot'),
                        $xmlBuilder->text(
                            $xmlBuilder->contentLike(
                                <<<EOF
                                So, this language. It's like, a programming language. Or is it a
                                scripting language? All is revealed in this thrilling horror spoof
                                of a documentary.
                                EOF
                            ),
                        ),
                    ),
                    $xmlBuilder->add(
                        $xmlBuilder->name('great-lines'),
                        $xmlBuilder->add(
                            $xmlBuilder->eachLike(),
                            $xmlBuilder->name('line'),
                            $xmlBuilder->text(
                                $xmlBuilder->contentLike('PHP solves all my web problems'),
                            ),
                        ),
                    ),
                    $xmlBuilder->add(
                        $xmlBuilder->name('rating'),
                        $xmlBuilder->attribute('type', 'thumbs'),
                        $xmlBuilder->text(
                            $xmlBuilder->contentLike(7),
                        ),
                    ),
                    $xmlBuilder->add(
                        $xmlBuilder->name('rating'),
                        $xmlBuilder->attribute('type', 'stars'),
                        $xmlBuilder->text(
                            $xmlBuilder->contentLike(5),
                        ),
                    ),
                ),
            );

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
