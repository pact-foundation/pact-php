<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use PhpPact\Consumer\Model\ConsumerRequest;

final class RequestBuilder implements RequestBuilderInterface
{
    public function __construct(private ParserInterface $parser)
    {
    }

    public function build(ConsumerRequest $request, array $data): void
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'method':
                    $request->setMethod($data['method']);
                    break;

                case 'path':
                    $request->setPath($data['path']);
                    break;

                case 'query':
                    $request->setQuery($this->parser->parseQueryString($data['query']));
                    break;

                case 'headers':
                    $request->setHeaders($this->parser->parseHeaders($data['headers']));
                    break;

                case 'raw headers':
                    $request->setHeaders($this->parser->parseHeaders($data['raw headers'], true));
                    break;

                case 'body':
                    $request->setBody($this->parser->parseBody($data['body'], $request->getBody()?->getContentType()));
                    break;

                case 'content type':
                    $request->addHeader('Content-Type', $data['content type']);
                    break;

                default:
                    break;
            }
        }
    }
}
