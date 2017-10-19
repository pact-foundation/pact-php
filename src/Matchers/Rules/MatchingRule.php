<?php

namespace PhpPact\Matchers\Rules;


class MatchingRule
{
    private $_jsonPath;
    private $_type;
    private $_regexPattern;
    private $_min;

    public function __construct($jsonPath, $options = array())
    {
        $this->_jsonPath = $jsonPath;

        if (!isset($options[MatcherRuleTypes::MIN_COUNT]) && !isset($options[MatcherRuleTypes::RULE_TYPE])) {
            throw new \Exception("Matching Rule options were not set to either a min value or an rule type");
        }

        if (isset($options[MatcherRuleTypes::MIN_COUNT])) {
            $this->_min = intval(MatcherRuleTypes::MIN_COUNT);
        }

        if ($options[MatcherRuleTypes::RULE_TYPE] == MatcherRuleTypes::REGEX_TYPE && isset($options[MatcherRuleTypes::REGEX_PATTERN])) {
            $this->_type = MatcherRuleTypes::REGEX_TYPE;
            $this->_regexPattern = $options[MatcherRuleTypes::REGEX_PATTERN];
        }
        if ($options[MatcherRuleTypes::RULE_TYPE] == MatcherRuleTypes::OBJECT_TYPE) {
            $this->_type = MatcherRuleTypes::OBJECT_TYPE;
        }
        else {
            throw new \Exception("Invalid rule type and options set: " . $options[MatcherRuleTypes::RULE_TYPE] );
        }
    }

    /**
     * Return the type of matching rule: regex | type
     *
     * @return string|false
     */
    public function getType()
    {
        return isset($this->_type) ? $this->_type : false;
    }

    /**
     * Get the integer value for the number of matches
     *
     * @return int|false
     */
    public function getMin()
    {
        return isset($this->_min) ? $this->_min : false;
    }

    /**
     * Get the JSON path to query
     *
     * @return string
     */
    public function getJsonPath()
    {
        return $this->_jsonPath;
    }

    /**
     * @return string|mixed
     */
    public function getRegexPattern()
    {
        return isset($this->_regexPattern) ? $this->_regexPattern : false;
    }

    public function jsonSerialize()
    {
        $obj = new \stdClass();
        $obj->{"{$this->getJsonPath()}"} = new \stdClass();

        if ($this->getMin()) {
            $obj->{"{$this->getJsonPath()}"}->min = $this->getMin();
        }

        if ($this->getType()) {
            $obj->{"{$this->getJsonPath()}"}->match = $this->getType();
        }

        if ($this->getRegexPattern()) {
            $obj->{"{$this->getJsonPath()}"}->regex = $this->getRegexPattern();
        }
    }
}