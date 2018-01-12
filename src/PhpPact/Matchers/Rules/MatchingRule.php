<?php

namespace PhpPact\Matchers\Rules;

class MatchingRule implements \JsonSerializable
{
    private $_jsonPath;
    private $_type;
    private $_regexPattern;
    private $_min;

    public function __construct($jsonPath, $options = [])
    {
        $this->_jsonPath = $jsonPath;

        if (!isset($options[MatcherRuleTypes::MIN_COUNT]) &&
            !isset($options[MatcherRuleTypes::MAX_COUNT]) &&
            !isset($options[MatcherRuleTypes::RULE_TYPE])) {
            throw new \Exception('Matching Rule options were not set to either a min/max value or an rule type');
        }

        if (isset($options[MatcherRuleTypes::MIN_COUNT])) {
            $this->_min = (int) ($options[MatcherRuleTypes::MIN_COUNT]);
        }

        if (isset($options[MatcherRuleTypes::MAX_COUNT])) {
            $this->_max = (int) ($options[MatcherRuleTypes::MAX_COUNT]);
        }

        if (isset($options[MatcherRuleTypes::RULE_TYPE])) {
            if ($options[MatcherRuleTypes::RULE_TYPE] == MatcherRuleTypes::REGEX_TYPE && isset($options[MatcherRuleTypes::REGEX_PATTERN])) {
                $this->_type         = $options[MatcherRuleTypes::RULE_TYPE];
                $this->_regexPattern = $options[MatcherRuleTypes::REGEX_PATTERN];
            } elseif ($options[MatcherRuleTypes::RULE_TYPE] == MatcherRuleTypes::OBJECT_TYPE) {
                $this->_type = $options[MatcherRuleTypes::RULE_TYPE];
            } else {
                throw new \Exception('Invalid rule type and options set: ' . $options[MatcherRuleTypes::RULE_TYPE]);
            }
        }
    }

    /**
     * Return the type of matching rule: regex | type
     *
     * @return false|string
     */
    public function getType()
    {
        return isset($this->_type) ? $this->_type : false;
    }

    /**
     * Get the integer value for the min number of matches
     *
     * @return false|int
     */
    public function getMin()
    {
        return isset($this->_min) ? $this->_min : false;
    }

    /**
     * Get the integer value for the max number of matches
     *
     * @return false|int
     */
    public function getMax()
    {
        return isset($this->_max) ? $this->_max : false;
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
     * Set the JSON path to query
     *
     * @param mixed $jsonPath
     *
     * @return string
     */
    public function setJsonPath($jsonPath)
    {
        $this->_jsonPath = $jsonPath;

        return $this;
    }

    /**
     * @return mixed|string
     */
    public function getRegexPattern()
    {
        return isset($this->_regexPattern) ? $this->_regexPattern : false;
    }

    public function jsonSerialize()
    {
        $obj = new \stdClass();

        if ($this->getMin()) {
            $obj->min = $this->getMin();
        }

        if ($this->getType()) {
            $obj->match = $this->getType();
        }

        if ($this->getRegexPattern()) {
            $obj->regex = $this->getRegexPattern();
        }

        return $obj;
    }
}
