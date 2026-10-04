<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\Body\Binary;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\ConsumerRequest;

final class RequestBuilder implements RequestBuilderInterface
{
    public function __construct(private ParserInterface $parser)
    {
    }

    /**
     * @param array<array-key, string|int> $data
     */
    public function build(ConsumerRequest $request, array $data): void
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'method':
                    $request->setMethod((string) $data['method']);
                    break;

                case 'path':
                    $request->setPath((string) $data['path']);
                    break;

                case 'query':
                    $request->setQuery($this->parser->parseQueryString((string) $data['query']));
                    break;

                case 'headers':
                    $request->setHeaders($this->parser->parseHeaders((string) $data['headers']));
                    break;

                case 'raw headers':
                    $request->setHeaders($this->parser->parseHeaders((string) $data['raw headers'], true));
                    break;

                case 'body':
                    $currentBody = $request->getBody();
                    $contentType = ($currentBody instanceof Text || $currentBody instanceof Binary) ? $currentBody->getContentType() : null;
                    $request->setBody($this->parser->parseBody((string) $data['body'], $contentType));
                    break;

                case 'content type':
                    $request->addHeader('Content-Type', (string) $data['content type']);
                    break;

                default:
                    break;
            }
        }
    }
}
