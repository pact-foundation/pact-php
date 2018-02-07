<?php

namespace PhpPact\Consumer\Matcher;

/**
 * Class RegexMatcher
 */
class RegexMatcher implements MatcherInterface
{
    /** @var mixed */
    private $value;

    /** @var string */
    private $regex;

    /**
     * RegexMatcher constructor.
     *
     * @param array|float|int|string $value value that will be matched
     * @param string                 $regex regex to be used
     */
    public function __construct($value, string $regex)
    {
        $this->value = $value;
        $this->regex = $regex;
    }

    /** @inheritdoc */
    public function getValue()
    {
        return $this->value;
    }

    /** @inheritdoc */
    public function jsonSerialize()
    {
        if (\is_array($this->getValue())) {
            $data['contents']['json_class'] = 'Pact::ArrayLike';

            foreach ($this->getValue() as $key => $value) {
                $data['contents'][$key] = $this->generateTerm($value);
            }
        } else {
            $data = $this->generateTerm($this->getValue());
        }

        return $data;
    }

    private function generateTerm($value)
    {
        return [
            'json_class' => 'Pact::Term',
            'data'       => [
                'generate' => $value,
                'matcher'  => [
                    'json_class' => 'Regexp',
                    'o'          => 0,
                    's'          => $this->regex
                ]
            ]
        ];
    }
}
