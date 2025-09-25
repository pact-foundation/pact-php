<?php

namespace XmlConsumer\Tests\Service;

use PhpPact\Consumer\Matcher\Matcher;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Xml\XmlBuilder;
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
        $matcher = new Matcher();

        $request = new ConsumerRequest();
        $request
            ->setMethod('GET')
            ->setPath('/movies')
            ->addHeader('Accept', $matcher->regex('application/xml', 'application\/.*xml'));

        $xmlBuilder = new XmlBuilder('1.0', 'UTF-8');
        $xmlBuilder
            ->root(
                $xmlBuilder->name('movies'),
                $xmlBuilder->content('List of movies'),
                $xmlBuilder->eachLike(
                    $xmlBuilder->examples(1),
                    $xmlBuilder->name('movie'),
                    $xmlBuilder->add(
                        $xmlBuilder->name('title'),
                        $xmlBuilder->contentLike('Big Buck Bunny'),
                    ),
                    $xmlBuilder->add(
                        $xmlBuilder->name('characters'),
                        $xmlBuilder->eachLike(
                            $xmlBuilder->examples(2),
                            $xmlBuilder->name('character'),
                            $xmlBuilder->add(
                                $xmlBuilder->name('name'),
                                $xmlBuilder->contentLike('Big Buck Bunny'),
                            ),
                            $xmlBuilder->add(
                                $xmlBuilder->name('actor'),
                                $xmlBuilder->contentLike('Jan Morgenstern'),
                            ),
                        ),
                    ),
                    $xmlBuilder->add(
                        $xmlBuilder->name('plot'),
                        $xmlBuilder->contentLike(
                            $plot = <<<EOF
                            The plot follows a day in the life of Big Buck Bunny, during which time he meets three bullying rodents: the leader, Frank the flying squirrel, and his sidekicks Rinky the red squirrel and Gimera the chinchilla.
                            The rodents amuse themselves by harassing helpless creatures of the forest by throwing fruits, nuts, and rocks at them.
                            EOF
                        ),
                    ),
                    $xmlBuilder->add(
                        $xmlBuilder->name('great-lines'),
                        $xmlBuilder->eachLike(
                            $xmlBuilder->name('line'),
                            $xmlBuilder->contentLike('Open source movie'),
                        ),
                    ),
                    $xmlBuilder->add(
                        $xmlBuilder->name('rating'),
                        $xmlBuilder->attribute('type', $matcher->regex('stars', 'stars|thumbs')),
                        $xmlBuilder->contentLike(6),
                    ),
                    // Generators
                    $xmlBuilder->add(
                        $xmlBuilder->name('release-date'),
                        $xmlBuilder->content($matcher->date('dd-MM-yyyy')),
                        $xmlBuilder->add(
                            $xmlBuilder->name('china'),
                            $xmlBuilder->content($matcher->date('dd-MM-yyyy')),
                        ),
                    ),
                    $xmlBuilder->add(
                        $xmlBuilder->name('specs'),
                        $xmlBuilder->attribute('runtime', $matcher->regex(null, '\d+ hours [0-5]?[0-9] minutes')),
                        $xmlBuilder->attribute('aspect-ratio', $matcher->regex(null, '([0-9]+[.])?[0-9]+:[0-9]+')),
                        $xmlBuilder->attribute('color', $matcher->regex(null, 'color|black&white')),
                    ),
                    $xmlBuilder->add(
                        $xmlBuilder->name('also-known-as'),
                        $xmlBuilder->content($matcher->string()),
                        $xmlBuilder->attribute('xmlns:aka', 'http://example.com/movies'),
                        $xmlBuilder->add(
                            $xmlBuilder->name('aka:united-states'),
                            $xmlBuilder->content($matcher->string()),
                        ),
                        $xmlBuilder->add(
                            $xmlBuilder->name('aka:australia'),
                            $xmlBuilder->content($matcher->string()),
                        ),
                        $xmlBuilder->add(
                            $xmlBuilder->name('aka:argentina'),
                            $xmlBuilder->content($matcher->string()),
                        ),
                        $xmlBuilder->add(
                            $xmlBuilder->name('aka:brazil'),
                            $xmlBuilder->content($matcher->string()),
                        ),
                    ),
                ),
            );

        $response = new ProviderResponse();
        $response
            ->setStatus(200)
            ->addHeader('Content-Type', $matcher->regex('application/xml', 'application\/.*xml'))
            ->setBody(new Text(json_encode($xmlBuilder), 'application/xml'));

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
        $movies = new \SimpleXMLElement($service->getMovies());
        $verifyResult = $builder->verify();

        $this->assertTrue($verifyResult);
        $this->assertCount(1, $movies->movie);
        $this->assertXmlContent('Big Buck Bunny', $movies->movie[0]->title);
        // TODO: investigate why mock server replace "\r\n" by "\n" on Windows
        $this->assertXmlContent(str_replace("\r\n", "\n", $plot), $movies->movie[0]->plot);
        $this->assertXmlContent('Open source movie', $movies->movie[0]->{'great-lines'}->line);
        $this->assertXmlContent('6', $movies->movie[0]->rating);
        $this->assertXmlAttribute('stars', 'type', $movies->movie[0]->rating);
        $this->assertCount(2, $movies->movie[0]->characters->character);
        $this->assertXmlContent('Big Buck Bunny', $movies->movie[0]->characters->character[0]->name);
        $this->assertXmlContent('Jan Morgenstern', $movies->movie[0]->characters->character[0]->actor);
        // TODO: investigate why content of the second element (examples = 2) is empty
        $this->assertXmlContent('', $movies->movie[0]->characters->character[1]->name);
        $this->assertXmlContent('', $movies->movie[0]->characters->character[1]->actor);
        // Generators
        $this->assertXmlContentGenerated($movies->movie[0]->{'release-date'});
        $this->assertXmlContentGenerated($movies->movie[0]->{'release-date'}[0]->china);
        $this->assertXmlAttributeGenerated('runtime', $movies->movie[0]->specs);
        $this->assertXmlAttributeGenerated('aspect-ratio', $movies->movie[0]->specs);
        $this->assertXmlAttributeGenerated('color', $movies->movie[0]->specs);
        $this->assertXmlContentGenerated($movies->movie[0]->{'also-known-as'});
        $this->assertXmlContentGenerated($movies->movie[0]->{'also-known-as'}->children('aka', true)->{'united-states'});
        $this->assertXmlContentGenerated($movies->movie[0]->{'also-known-as'}->children('aka', true)->{'australia'});
        $this->assertXmlContentGenerated($movies->movie[0]->{'also-known-as'}->children('aka', true)->{'argentina'});
        $this->assertXmlContentGenerated($movies->movie[0]->{'also-known-as'}->children('aka', true)->{'brazil'});
    }

    private function assertXmlContent(string $expected, \SimpleXMLElement $element): void
    {
        $this->assertCount(1, $element);
        $this->assertSame($expected, (string)$element[0]);
    }

    private function assertXmlContentGenerated(\SimpleXMLElement $element): void
    {
        $this->assertCount(1, $element);
        $this->assertNotSame('', (string)$element[0]);
    }

    private function assertXmlAttribute(string $expected, string $key, \SimpleXMLElement $element): void
    {
        $this->assertCount(1, $element);
        $attributes = $element[0]->attributes();
        $this->assertNotNull($attributes);
        $this->assertSame($expected, (string)$attributes[$key]);
    }

    private function assertXmlAttributeGenerated(string $key, \SimpleXMLElement $element): void
    {
        $this->assertCount(1, $element);
        $attributes = $element[0]->attributes();
        $this->assertNotNull($attributes);
        $this->assertNotSame('', (string)$attributes[$key]);
    }
}
