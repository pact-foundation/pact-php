<?php

namespace PhpPactTest\CompatibilitySuite\ServiceContainer;

use PhpPactTest\CompatibilitySuite\Service\BodyStorage;
use PhpPactTest\CompatibilitySuite\Service\BodyValidator;
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
        $this->set('generator_parser', new GeneratorParser($this->get('fixture_loader')));
        $this->set('generator_converter', new GeneratorConverter());
        $this->set('generator_server', new GeneratorServer());
        $this->set('body_storage', new BodyStorage());
        $this->set('body_validator', new BodyValidator($this->get('body_storage')));
        $this->set('message_pact_writer', new MessagePactWriter($this->get('parser'), $this->getSpecification()));
        $this->set('request_generator_builder', new RequestGeneratorBuilder($this->get('generator_parser'), $this->get('generator_converter')));
        $this->set('response_generator_builder', new ResponseGeneratorBuilder($this->get('generator_parser'), $this->get('generator_converter')));
        $this->set('message_generator_builder', new MessageGeneratorBuilder($this->get('generator_parser'), $this->get('generator_converter')));
    }

    protected function getSpecification(): string
    {
        return '3.0.0';
    }
}
