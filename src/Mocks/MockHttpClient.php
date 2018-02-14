<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 2/14/2018
 * Time: 8:03 AM
 */

namespace PhpPact\Mocks;

use PhpPact\Mocks\MockHttpService\MockProviderHost;
use Psr\Http\Message\ResponseInterface;

/**
 * A mock client to wrap the sendRequest to call the mock server using \Jfalque\HttpMock\ServerInterface
 * Class MockHttpClient
 * @package PhpPact\Mocks
 */
class MockHttpClient
{

    /**
     * @var MockProviderHost
     */
    private $_mockHost;

    /**
     * MockHttpClient constructor.
     * @param MockProviderHost $mockHost
     */
    public function __construct(&$mockHost)
    {
        $this->_mockHost = $mockHost;
    }

    /**
     * @param RequestInterface $httpRequest
     * @return callable|null|ResponseInterface
     */
    public function sendRequest($httpRequest)
    {
        return $this->_mockHost->handle($httpRequest);
    }
}