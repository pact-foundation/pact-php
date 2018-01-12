<?php
/**
 * Created by PhpStorm.
 * Date: 10/30/2017
 * Time: 10:57 AM
 */

namespace Mocks\MockHttpService\Matchers;

use PhpPact\Mocks\MockHttpService\Matchers\XmlHttpBodyMatchChecker;
use PHPUnit\Framework\TestCase;

class XmlHttpBodyMatchCheckerTest extends TestCase
{
    public function testModifyPathForXmlAttributes()
    {
        $xmlHttpBodyMatchChecker = new XmlHttpBodyMatchChecker(false);

        $original = "$.body.animals[*].alligator['@phoneNumber']";
        $expected = '$.body.animals.alligator[*].@attributes.phoneNumber';
        $actual   = $xmlHttpBodyMatchChecker->modifyPathForXmlAttributes($original);

        $this->assertEquals($expected, $actual, 'XML with attributes should be transformed');
    }
}
