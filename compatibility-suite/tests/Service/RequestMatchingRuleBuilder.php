<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use JsonPath\JsonObject;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\ConsumerRequest;
use PhpPactTest\CompatibilitySuite\Exception\IntegrationJsonFormatException;

final class RequestMatchingRuleBuilder implements RequestMatchingRuleBuilderInterface
{
    public function __construct(
        private MatchingRuleParserInterface $parser,
        private MatchingRuleConverterInterface $converter
    ) {
    }

    public function build(ConsumerRequest $request, string $file): void
    {
        foreach ($this->parser->parse($file) as $rule) {
            switch ($rule->getCategory()) {
                case 'method':
                    // I don't think method support matching rule, at least in pact-php.
                    break;

                case 'path':
                    $matcher = $this->converter->convert($rule, $request->getPath());
                    if ($matcher) {
                        $request->setPath($matcher);
                    }
                    break;

                case 'query':
                    if ($rule->getSubCategory()) {
                        $queryValues = $request->getQuery()[$rule->getSubCategory()];
                        $matcher = $this->converter->convert($rule, $queryValues);
                        if ($matcher) {
                            $request->addQueryParameter($rule->getSubCategory(), $matcher);
                        }
                    }
                    break;

                case 'header':
                    if ($rule->getSubCategory()) {
                        $headerValues = array_change_key_case($request->getHeaders())[$rule->getSubCategory()];
                        $matcher = $this->converter->convert($rule, $headerValues);
                        if ($matcher) {
                            $request->addHeader($rule->getSubCategory(), $matcher);
                        }
                    }
                    break;

                case 'body':
                    $body = $request->getBody();
                    if ($body instanceof Text && $body->getContentType() === 'application/json') {
                        $jsonObject = new JsonObject($body->getContents(), true);
                        $value = $jsonObject->{$rule->getSubCategory()};
                        if (str_contains($rule->getSubCategory(), '*')) {
                            $value = reset($value); // This is for handling '$.two.*.ids' and '$.*'
                        }
                        $matcher = $this->converter->convert($rule, $value);
                        if ($matcher) {
                            $jsonObject->{$rule->getSubCategory()} = $matcher;
                            $body->setContents($jsonObject);
                        }
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
