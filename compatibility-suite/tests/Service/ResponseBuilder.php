<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\ProviderResponse;

final class ResponseBuilder implements ResponseBuilderInterface
{
    public function __construct(private ParserInterface $parser)
    {
    }

    /**
     * @param array<array-key, string|int> $data
     */
    public function build(ProviderResponse $response, array $data): void
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'response':
                    $response->setStatus((int) $data['response']);
                    break;

                case 'response headers':
                    $response->setHeaders($this->parser->parseHeaders((string) $data['response headers']));
                    break;

                case 'response body':
                    $currentBody = $response->getBody();
                    $contentType = ($currentBody instanceof Text || $currentBody instanceof Binary) ? $currentBody->getContentType() : null;
                    $response->setBody($this->parser->parseBody((string) $data['response body'], $contentType));
                    break;

                case 'response content':
                    if (!empty($data['response content'])) {
                        $response->addHeader('Content-Type', (string) $data['response content']);
                    }
                    break;

                default:
                    break;
            }
        }
    }
}
