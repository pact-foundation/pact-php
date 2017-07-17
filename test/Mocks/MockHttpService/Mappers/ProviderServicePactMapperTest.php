<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 6/28/2017
 * Time: 3:52 PM
 */

namespace Mocks\MockHttpService\Mappers;

use PhpPact\Mocks\MockHttpService\Mappers\ProviderServicePactMapper;
use PHPUnit\Framework\TestCase;

class ProviderServicePactMapperTest extends TestCase
{
    public function testConvert() {

        // most of this is covered by other unit tests.  Basic coverage provided below
        $mapper = new ProviderServicePactMapper();

        $obj = new \stdClass();
        $obj->provider = new \stdClass();
        $obj->provider->name = "MyProvider";
        $obj->consumer = new \stdClass();
        $obj->consumer->name = "MyConsumer";
        $obj->metadata = new \stdClass();
        $obj->metadata->pactSpecificationVersion = "4.1";
        $obj->interactions = array();

        $providerServicePactFile = $mapper->Convert($obj);
        $this->assertEquals('MyProvider', $providerServicePactFile->getProvider()->getName(),"Provider name should be set");
        $this->assertEquals('4.1', $providerServicePactFile->getMetadata()->pactSpecificationVersion,"Specification should be set");

        $json = '{ "provider": { "name": "ProviderApi" }, "consumer": { "name": "ApiConsumer" },"interactions": [], "metadata": { "pactSpecificationVersion": "1.1.0"  } }';
        $providerServicePactFile = $mapper->Convert($json);
        $this->assertEquals('ApiConsumer', $providerServicePactFile->getConsumer()->getName(),"Consumer name should be set");
        $this->assertEquals('1.1.0', $providerServicePactFile->getMetadata()->pactSpecificationVersion,"Specification should be set");
    }
}
