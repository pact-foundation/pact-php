<?php

namespace PhpPactTest\IntegrationTests\Models;

use PhpPact\Mocks\MockHttpService\Comparers\ProviderServiceRequestComparer;
use PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceRequestMapper;
use PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest;
use PHPUnit\Framework\TestCase;

class RequestTestCase extends TestCase
{
    private $_match;
    private $_comment;

    /**
     * @var ProviderServiceRequest
     */
    private $_expected;

    /**
     * @var ProviderServiceRequest
     */
    private $_actual;

    /**
     * @var ProviderServiceRequestComparer
     */
    private $_requestComparer;

    /**
     * Using this function to avoid overriding PHPUnitTestCase constructors
     *
     * @param $json
     */
    public function initialize($json)
    {
        $this->_requestComparer = new ProviderServiceRequestComparer();

        $jsonObj = \json_decode($json);
        if (isset($jsonObj->match)) {
            $this->setMatch($jsonObj->match);
        }

        if (isset($jsonObj->comment)) {
            $this->setComment($jsonObj->comment);
        }

        $mapper = new ProviderServiceRequestMapper();
        if (isset($jsonObj->expected)) {
            // cast $json->expected
            $this->setExpected($mapper->convert($jsonObj->expected));
        }

        if (isset($jsonObj->actual)) {
            // cast $json->actual
            $this->setActual($mapper->convert($jsonObj->actual));
        }
    }

    /**
     * @return mixed
     */
    public function getMatch()
    {
        return $this->_match;
    }

    /**
     * @param mixed $match
     *
     * @return RequestTestCase
     */
    public function setMatch($match)
    {
        $this->_match = $match;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->_comment;
    }

    /**
     * @param mixed $comment
     *
     * @return RequestTestCase
     */
    public function setComment($comment)
    {
        $this->_comment = $comment;

        return $this;
    }

    /**
     * @return ProviderServiceRequest
     */
    public function getExpected(): ProviderServiceRequest
    {
        return $this->_expected;
    }

    /**
     * @param ProviderServiceRequest $expected
     *
     * @return RequestTestCase
     */
    public function setExpected(ProviderServiceRequest $expected): self
    {
        $this->_expected = $expected;

        return $this;
    }

    /**
     * @return ProviderServiceRequest
     */
    public function getActual(): ProviderServiceRequest
    {
        return $this->_actual;
    }

    /**
     * @param ProviderServiceRequest $actual
     *
     * @return RequestTestCase
     */
    public function setActual(ProviderServiceRequest $actual): self
    {
        $this->_actual = $actual;

        return $this;
    }

    /**
     * @param $filePath
     */
    public function verify($filePath)
    {
        $result = $this->_requestComparer->compare($this->_expected, $this->_actual);

        if ($this->_match) {
            $this->assertFalse($result->hasFailure(), 'There should not be any errors' . $filePath);
        } else {
            $this->assertGreaterThanOrEqual(1, \count($result->failures()), 'There should be at least one failure: ' . $filePath);
        }
    }
}
