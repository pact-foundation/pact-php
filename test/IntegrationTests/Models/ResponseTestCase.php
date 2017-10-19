<?php

namespace PhpPactTest\IntegrationTests\Models;

use PHPUnit\Framework\TestCase;
use \PhpPact\Mocks\MockHttpService\Comparers\ProviderServiceResponseComparer;
use \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse;
use \PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceResponseMapper;

class ResponseTestCase extends TestCase
{
    private $_match;
    private $_comment;

    /**
     * @var ProviderServiceResponse
     */
    private $_expected;

    /**
     * @var ProviderServiceResponse
     */
    private $_actual;

    /**
     * @var ProviderServiceResponseComparer
     */
    private $_responseComparer;

    /**
     * Using this function to avoid overriding PHPUnitTestCase constructors
     * @param $json
     */
    public function Initialize($json)
    {
        $this->_responseComparer = new ProviderServiceResponseComparer();

        $jsonObj = \json_decode($json);
        if (isset($jsonObj->match)) {
            $this->setMatch($jsonObj->match);
        }

        if (isset($jsonObj->comment)) {
            $this->setComment($jsonObj->comment);
        }

        $mapper = new ProviderServiceResponseMapper();
        if (isset($jsonObj->expected)) {
            // cast $json->expected
            $this->setExpected($mapper->Convert($jsonObj->expected));
        }

        if (isset($jsonObj->actual)) {
            // cast $json->actual
            $this->setActual($mapper->Convert($jsonObj->actual));
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
     */
    public function setMatch($match)
    {
        $this->_match = $match;
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
     */
    public function setComment($comment)
    {
        $this->_comment = $comment;
    }

    /**
     * @return ProviderServiceResponse
     */
    public function getExpected(): ProviderServiceResponse
    {
        return $this->_expected;
    }

    /**
     * @param ProviderServiceResponse $expected
     */
    public function setExpected(ProviderServiceResponse $expected)
    {
        $this->_expected = $expected;
    }

    /**
     * @return ProviderServiceResponse
     */
    public function getActual(): ProviderServiceResponse
    {
        return $this->_actual;
    }

    /**
     * @param ProviderServiceResponse $actual
     */
    public function setActual(ProviderServiceResponse $actual)
    {
        $this->_actual = $actual;
    }

    public function Verify()
    {
        $result = $this->_responseComparer->Compare($this->_expected, $this->_actual);

        if ($this->_match) {
            $this->assertFalse($result->HasFailure(), "There should not be any errors");
        } else {
            $this->assertGreaterThanOrEqual(1, count($result->Failures()));
        }
    }
}
