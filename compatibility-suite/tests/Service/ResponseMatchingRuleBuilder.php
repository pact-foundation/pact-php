<?php

namespace PhpPactTest\CompatibilitySuite\Service;

use JsonPath\JsonObject;
use PhpPact\Consumer\Model\Body\Text;
use PhpPact\Consumer\Model\ProviderResponse;
use PhpPactTest\CompatibilitySuite\Exception\IntegrationJsonFormatException;

final class ResponseMatchingRuleBuilder implements ResponseMatchingRuleBuilderInterface
{
    public function __construct(
        private MatchingRuleParserInterface $parser,
        private MatchingRuleConverterInterface $converter
    ) {
    }

    public function build(ProviderResponse $response, string $file): void
    {
        foreach ($this->parser->parse($file) as $rule) {
            switch ($rule->getCategory()) {
                case 'status':
                    $response->setStatus($this->converter->convert($rule, $response->getStatus()));
                    break;

                case 'header':
                    if ($rule->getSubCategory()) {
                        $headerValues = array_change_key_case($response->getHeaders())[$rule->getSubCategory()];
                        $matcher = $this->converter->convert($rule, $headerValues);
                        if ($matcher) {
                            $response->addHeader($rule->getSubCategory(), $matcher);
                        }
                    }
                    break;

                case 'body':
                    $body = $response->getBody();
                    if ($body instanceof Text && $body->getContentType() === 'application/json') {
                        $jsonObject = new JsonObject($body->getContents(), true);
                        $matcher = $this->converter->convert($rule, $jsonObject->{$rule->getSubCategory()});
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
