<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use JsonPath\JsonObject;
use PhpPact\Consumer\Model\Body\Text;
use PhpPactTest\CompatibilitySuite\Exception\IntegrationJsonFormatException;
use PhpPactTest\CompatibilitySuite\Model\Message;

final class MessageGeneratorBuilder implements MessageGeneratorBuilderInterface
{
    public function __construct(
        private GeneratorParserInterface $parser,
        private GeneratorConverterInterface $converter
    ) {
    }

    public function build(Message $message, string $value): void
    {
        foreach ($this->parser->parse($value) as $generator) {
            switch ($generator->getCategory()) {
                case 'metadata':
                    $metadata = $message->getMetadata() ?? [];
                    $metadata[$generator->getSubCategory()] = $this->converter->convert($generator);
                    $message->setMetadata($metadata);
                    break;

                case 'body':
                    $body = $message->getBody();
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
