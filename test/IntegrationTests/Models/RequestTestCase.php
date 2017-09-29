<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 9/29/2017
 * Time: 3:51 PM
 */

namespace PhpPactTest\IntegrationTests\Models;

use PHPUnit\Framework\TestCase;
use \PhpPact\Mocks\MockHttpService\Comparers\ProviderServiceRequestComparer;
use \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest;
use \PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceRequestMapper;


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
     * @param $json
     */
    public function Initialize($json) {
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
     * @return RequestTestCase
     */
    public function setExpected(ProviderServiceRequest $expected): RequestTestCase
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
     * @return RequestTestCase
     */
    public function setActual(ProviderServiceRequest $actual): RequestTestCase
    {
        $this->_actual = $actual;
        return $this;
    }




    public function Verify()
    {
        $result = $this->_requestComparer->Compare($this->_expected, $this->_actual);

        if ($this->_match) {
            $this->assertFalse($result->HasFailure(), "There should not be any errors");
        } else {
            $this->assertEquals(1, count($result->Failures()));
        }
    }
}