<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use JsonPath\JsonObject;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPactTest\CompatibilitySuite\Exception\IntegrationJsonFormatException;

final class ResponseGeneratorBuilder implements ResponseGeneratorBuilderInterface
{
    public function __construct(
        private GeneratorParserInterface $parser,
        private GeneratorConverterInterface $converter
    ) {
    }

    public function build(ProviderResponse $response, string $value): void
    {
        foreach ($this->parser->parse($value) as $generator) {
            switch ($generator->getCategory()) {
                case 'status':
                    $response->setStatus($this->converter->convert($generator));
                    break;

                case 'header':
                    if ($generator->getSubCategory()) {
                        $response->addHeader($generator->getSubCategory(), $this->converter->convert($generator));
                    }
                    break;

                case 'body':
                    $body = $response->getBody();
                    if ($body instanceof Text && $body->getContentType() === 'application/json') {
                        $jsonObject = new JsonObject($body->getContents(), true);
                        $jsonObject->{$generator->getSubCategory()} = $this->converter->convert($generator);
                        $body->setContents($jsonObject);
                    } else {
                        throw new IntegrationJsonFormatException("Integration JSON format doesn't support non-JSON format");
                    }
                    break;

                default:
                    break;
            }
        }
    }
}
