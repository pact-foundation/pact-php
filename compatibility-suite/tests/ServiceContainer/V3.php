<?php

namespace PhpPactTest\CompatibilitySuite\ServiceContainer;

use PhpPactTest\CompatibilitySuite\Service\BodyStorage;
use PhpPactTest\CompatibilitySuite\Service\BodyValidator;
use PhpPactTest\CompatibilitySuite\Service\FixtureLoaderInterface;
use PhpPactTest\CompatibilitySuite\Service\GeneratorConverter;
use PhpPactTest\CompatibilitySuite\Service\GeneratorParser;
use PhpPactTest\CompatibilitySuite\Service\GeneratorServer;
use PhpPactTest\CompatibilitySuite\Service\MessageGeneratorBuilder;
use PhpPactTest\CompatibilitySuite\Service\MessagePactWriter;
use PhpPactTest\CompatibilitySuite\Service\RequestGeneratorBuilder;
use PhpPactTest\CompatibilitySuite\Service\ResponseGeneratorBuilder;

class V3 extends V2
{
    public function __construct()
    {
        parent::__construct();
        $fixtureLoader = $this->get('fixture_loader');
        assert($fixtureLoader instanceof FixtureLoaderInterface);

        $generatorParser = new GeneratorParser($fixtureLoader);
        $generatorConverter = new GeneratorConverter();
        $bodyStorage = new BodyStorage();

        $this->set('generator_parser', $generatorParser);
        $this->set('generator_converter', $generatorConverter);
        $this->set('generator_server', new GeneratorServer());
        $this->set('body_storage', $bodyStorage);
        $this->set('body_validator', new BodyValidator($bodyStorage));
        $this->set('message_pact_writer', new MessagePactWriter($this->getSpecification()));
        $this->set('request_generator_builder', new RequestGeneratorBuilder($generatorParser, $generatorConverter));
        $this->set('response_generator_builder', new ResponseGeneratorBuilder($generatorParser, $generatorConverter));
        $this->set('message_generator_builder', new MessageGeneratorBuilder($generatorParser, $generatorConverter));
    }

    protected function getSpecification(): string
    {
        return '3.0.0';
    }
}
