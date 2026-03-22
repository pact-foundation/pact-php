<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidFormatterException;
use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Formatters\Expression\ExpressionFormatter;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

class MatchAll extends AbstractMatcher implements JsonFormattableInterface, ExpressionFormattableInterface
{
    /**
     * @param array<mixed>|object $value
     * @param MatcherInterface[] $matchers
     */
    public function __construct(private object|array $value, private array $matchers)
    {
        $this->setMatchers($matchers);
        parent::__construct();
    }

    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:matcher:type' => array_map(
                $this->getFormatter()->format(...),
                $this->matchers
            ),
            'value' => $this->value,
        ]);
    }

    public function formatExpression(): Expression
    {
        $formatter = $this->getFormatter();
        if (!$formatter instanceof ExpressionFormatter) {
            throw new InvalidFormatterException(sprintf("Instance of '%s' is required to format expression", ExpressionFormatter::class));
        }
        return new Expression(implode(', ', array_map(
            $formatter->format(...),
            $this->matchers
        )));
    }

    /**
     * @param MatcherInterface[] $matchers
     */
    private function setMatchers(array $matchers): void
    {
        if (empty($matchers)) {
            throw new InvalidValueException('Matchers should not be empty');
        }
        $this->matchers = [];
        foreach ($matchers as $matcher) {
            if ($matcher instanceof self) {
                throw new MatcherNotSupportedException("Nested 'matcherAll' matcher is not supported");
            }
            $this->addMatcher($matcher);
        }
    }

    private function addMatcher(MatcherInterface $matcher): void
    {
        $this->matchers[] = $matcher;
    }
}
