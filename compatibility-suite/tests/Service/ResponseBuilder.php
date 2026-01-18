<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\ProviderResponse;

final class ResponseBuilder implements ResponseBuilderInterface
{
    public function __construct(private ParserInterface $parser)
    {
    }

    public function build(ProviderResponse $response, array $data): void
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'response':
                    $response->setStatus($data['response']);
                    break;

                case 'response headers':
                    $response->setHeaders($this->parser->parseHeaders($data['response headers']));
                    break;

                case 'response body':
                    $response->setBody($this->parser->parseBody($data['response body'], $response->getBody()?->getContentType()));
                    break;

                case 'response content':
                    $response->addHeader('Content-Type', $data['response content']);
                    break;

                default:
                    break;
            }
        }
    }
}
