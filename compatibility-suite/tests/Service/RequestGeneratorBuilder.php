<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use JsonPath\JsonObject;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPactTest\CompatibilitySuite\Exception\IntegrationJsonFormatException;

final class RequestGeneratorBuilder implements RequestGeneratorBuilderInterface
{
    public function __construct(
        private GeneratorParserInterface $parser,
        private GeneratorConverterInterface $converter
    ) {
    }

    public function build(ConsumerRequest $request, string $value): void
    {
        foreach ($this->parser->parse($value) as $generator) {
            switch ($generator->getCategory()) {
                case 'method':
                    // Can't set generator to method
                    break;

                case 'path':
                    $request->setPath($this->converter->convert($generator));
                    break;

                case 'query':
                    if ($generator->getSubCategory()) {
                        $request->addQueryParameter($generator->getSubCategory(), $this->converter->convert($generator));
                    }
                    break;

                case 'header':
                    if ($generator->getSubCategory()) {
                        $request->addHeader($generator->getSubCategory(), $this->converter->convert($generator));
                    }
                    break;

                case 'body':
                    $body = $request->getBody();
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
