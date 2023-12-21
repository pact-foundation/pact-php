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
                case 'status':
                    $response->setStatus($data['status']);
                    break;

                case 'headers':
                    $response->setHeaders($this->parser->parseHeaders($data['headers']));
                    break;

                case 'body':
                    $response->setBody($this->parser->parseBody($data['body'], $response->getBody()?->getContentType()));
                    break;

                case 'content-type':
                    $response->addHeader('Content-Type', $data['content-type']);
                    break;

                default:
                    break;
            }
        }
    }
}
