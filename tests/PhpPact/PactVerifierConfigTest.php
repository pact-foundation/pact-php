<?php

use PhpPact\PactVerifierConfig;
use PHPUnit\Framework\TestCase;

class PactVerifierConfigTest extends TestCase
{
    public function testSetBaseUri()
    {
        $config = new PactVerifierConfig();

        $config->setBaseUri('localhost');
        $actual = $config->getBaseUri();
        $this->assertEquals('http://localhost', $actual, 'Ensure http is appended');

        $config->setBaseUri('http://localhost/');
        $actual = $config->getBaseUri();
        $this->assertEquals('http://localhost', $actual, 'Ensure http backslash is stripped');

        $config->setBaseUri('localhost', 80, 'https');
        $actual = $config->getBaseUri();
        $this->assertEquals('https://localhost', $actual, 'Ensure https does not have port appended');

        $config->setBaseUri('localhost', 443, 'https');
        $actual = $config->getBaseUri();
        $this->assertEquals('https://localhost', $actual, 'Ensure https does not have port appended.  Same with 443');

        $config->setBaseUri('localhost', 123, 'https');
        $actual = $config->getBaseUri();
        $this->assertEquals('https://localhost:123', $actual, 'Ensure https has a custom port');

        $config->setBaseUri('http://google.com', 123);
        $actual = $config->getBaseUri();
        $this->assertEquals('http://google.com:123', $actual, 'Ensure http has a custom port on google');
        $this->assertEquals("google.com", $config->getBaseUrn(), "Ensure URN is properly set");
        $this->assertEquals("123", $config->getPort(), "Ensure port is properly set");

        $config->setBaseUri('https://127.0.10.13:333/', 333, 'https');
        $actual = $config->getBaseUri();
        $this->assertEquals('https://127.0.10.13:333', $actual, 'Ensure IPs work along with keeping a core https override');
        $this->assertEquals("127.0.10.13", $config->getBaseUrn(), "Ensure URN is properly set");
        $this->assertEquals("333", $config->getPort(), "Ensure port is properly set");

        $config->setBaseUri('https://127.0.10.13:222/', 333, 'https');
        $actual = $config->getBaseUri();
        $this->assertEquals('https://127.0.10.13:222', $actual, 'The port embedded in the URL trumps what is passed in');
        $this->assertEquals("127.0.10.13", $config->getBaseUrn(), "Ensure URN is properly set");
        $this->assertEquals("222", $config->getPort(), "Ensure port is properly set");

        $config->setBaseUri('http://127.0.10.13:333/', 333, 'https');
        $actual = $config->getBaseUri();
        $this->assertEquals('http://127.0.10.13:333', $actual, 'The protocol embedded in the URL trumps what is passed in');
        $this->assertEquals("127.0.10.13", $config->getBaseUrn(), "Ensure URN is properly set");
        $this->assertEquals("333", $config->getPort(), "Ensure port is properly set");
    }
}
