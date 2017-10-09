<?php

namespace PhpPact\Matchers;

class MatcherCheck
{
    const PathPrefix = "$.";
    private $_path;

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * @param mixed $path
     * @return MatcherCheck
     */
    public function setPath($path)
    {
        if (substr($path, 0, strlen(static::PathPrefix)) != static::PathPrefix) {
            $path = static::PathPrefix . $path;
        }

        $this->_path = $path;
        return $this;
    }
}
