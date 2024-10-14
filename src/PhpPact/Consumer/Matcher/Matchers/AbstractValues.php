<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidValueException;
use PhpPact\Consumer\Matcher\Exception\MatcherNotSupportedException;
use PhpPact\Consumer\Matcher\Exception\MatchingExpressionException;
use PhpPact\Consumer\Matcher\Model\Attributes;
use PhpPact\Consumer\Matcher\Model\Expression;
use PhpPact\Consumer\Matcher\Model\Matcher\ExpressionFormattableInterface;
use PhpPact\Consumer\Matcher\Model\Matcher\JsonFormattableInterface;
use PhpPact\Consumer\Matcher\Model\MatcherInterface;

/**
 * Base class for EachKey or EachValue matchers
 */
abstract class AbstractValues extends AbstractMatcher implements JsonFormattableInterface, ExpressionFormattableInterface
{
    /**
     * @param array<mixed>|object $values
     * @param MatcherInterface[]  $rules
     */
    public function __construct(private object|array $values, private array $rules)
    {
        $this->setRules($rules);
        parent::__construct();
    }

    public function formatJson(): Attributes
    {
        return new Attributes([
            'pact:matcher:type' => $this->getType(),
            'rules' => array_map(
                fn (MatcherInterface $rule) => $this->getFormatter()->format($rule),
                $this->rules
            ),
            'value' => $this->values,
        ]);
    }

    public function formatExpression(): Expression
    {
        if (count($this->rules) !== 1) {
            throw new MatchingExpressionException(sprintf("Matcher '%s' only support 1 rule in expression, %d provided", $this->getType(), count($this->rules)));
        }
        $rule = reset($this->rules);
        if (!$rule instanceof ExpressionFormattableInterface) {
            throw new MatcherNotSupportedException(sprintf("Rule '%s' must implement '%s' to be formatted as expression", get_class($rule), ExpressionFormattableInterface::class));
        }

        return new Expression(sprintf('%s(%s)', $this->getType(), $rule->formatExpression()));
    }

    abstract protected function getType(): string;

    /**
     * @param MatcherInterface[] $rules
     */
    private function setRules(array $rules): void
    {
        if (empty($rules)) {
            throw new InvalidValueException("Rules should not be empty");
        }
        $this->rules = [];
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }
    }

    private function addRule(MatcherInterface $rule): void
    {
        $this->rules[] = $rule;
    }
}
