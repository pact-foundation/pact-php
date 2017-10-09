<?php

namespace PhpPact\Mocks\MockHttpService;

/**
 * Class MockProviderHost
 *
 * This is a mock host (server) that we "host" the mock provider service on.
 *
 * This is a wrapper class around \Jfalque\HttpMock\Server.  Because the library Server was final, some trickery was
 * was applied.   The biggest difference is that handle() throws an exception if the mock request was not found
 *
 * @package PhpPact\Mocks\MockHttpService
 *
 * @method MockProviderHost whenProcotolVersion(string|float|int $version)
 * @method MockProviderHost whenMethod(string|string[] $method)
 * @method MockProviderHost whenUri(string $uri, bool $regexp = false)
 * @method MockProviderHost whenScheme(string $scheme)
 * @method MockProviderHost whenHost(string $host, bool $regexp = false)
 * @method MockProviderHost whenPort(int|int[] $port)
 * @method MockProviderHost whenPath(string $path, bool $regexp = false)
 * @method MockProviderHost whenQuery(string|array $query, bool $regexpOrSubset = false)
 * @method MockProviderHost whenFragment(string $fragment, bool $regexp = false)
 * @method MockProviderHost whenHeaders(array $headers)
 * @method MockProviderHost whenBody(string $body, bool $regexp = false)
 * @method MockProviderHost andWhenProcotolVersion(string|float|int $version)
 * @method MockProviderHost andWhenMethod(string|string[] $method)
 * @method MockProviderHost andWhenUri(string $uri, bool $regexp = false)
 * @method MockProviderHost andWhenScheme(string $scheme)
 * @method MockProviderHost andWhenHost(string $host, bool $regexp = false)
 * @method MockProviderHost andWhenPort(int|int[] $port)
 * @method MockProviderHost andWhenPath(string $path, bool $regexp = false)
 * @method MockProviderHost andWhenQuery(string|array $query, bool $regexpOrSubset = false)
 * @method MockProviderHost andWhenFragment(string $fragment, bool $regexp = false)
 * @method MockProviderHost andWhenHeaders(array $headers)
 * @method MockProviderHost andWhenBody(string $body, bool $regexp = false)
 */
class MockProviderHost implements \Jfalque\HttpMock\ServerInterface
{
    const RESPONSE_KEY = 'response';
    const REQUEST_KEY = 'request';
    /**
     * @var \Jfalque\HttpMock\Server
     */
    private $_host;

    /**
     * @var array
     */
    private $_requestAndResponsePairs;

    public function __construct()
    {
        $this->_host = new \Jfalque\HttpMock\Server();

        $this->ClearRequestResponse();
    }

    public function __call($name, $arguments)
    {
        if ($name == 'handle') {
            return $this->handle($arguments);
        }


        if (is_callable(array($this->_host, $name))) {
            $this->_host = $this->_host->__call($name, $arguments);
            return $this;
        }

        throw new \BadMethodCallException(sprintf("This function is not available here or on the _host: %s", $name));
    }

    /**
     * Provide a wrapper round the  \Jfalque\HttpMock\Server handle() function.
     *
     * @param \Psr\Http\Message\RequestInterface $request
     * @return callable|null|\Psr\Http\Message\ResponseInterface
     * @throws \RuntimeException if a response cannot be found
     */
    public function handle(\Psr\Http\Message\RequestInterface $request)
    {
        $pair = array();
        $pair[self::REQUEST_KEY] = $request;

        $response = $this->_host->handle($request);

        if ($response) {
            $pair[self::RESPONSE_KEY] = $response;
            $this->_requestAndResponsePairs[] = $pair;
            return $response;
        }


        throw new \RuntimeException("No mock responses for were found for this request: " . $this->PrintRequest($request));
    }

    /**
     * Defines the result for the current matching layer.
     *
     * If the result is a callable, it must accept a {@see RequestInterface} as first parameter and return an instance
     * of {@see ResponseInterface}.
     *
     * @param \Psr\Http\Message\RequestInterface|callable $result
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function return($result)
    {
        $this->_host = $this->_host->return($result);
        return $this;
    }

    /**
     * Ends the current matching layer definition and returns its parent, if any.
     *
     * @return self|null
     */
    public function end()
    {
        $this->_host = $this->_host->end();
        return $this;
    }

    /**
     * @return array
     */
    public function getRequestAndResponsePairs()
    {
        return $this->_requestAndResponsePairs;
    }



    public function ClearRequestResponse()
    {
        $this->_requestAndResponsePairs = array();
    }

    private function PrintRequest(\Psr\Http\Message\RequestInterface $request)
    {
        $msg = "\nA " . $request->getMethod() . " request with URL: " . $request->getUri()->getHost() . "\n";
        $msg .= "\thas path: " . $request->getUri()->getPath() . "\n";
        $msg .= "\thas port: " . $request->getUri()->getPort() . "\n";
        $msg .= "\thas query: " . $request->getUri()->getQuery() . "\n";

        if (count($request->getHeaders()) > 0) {
            $msg .= "\thas headers: \n";
            $headers = $request->getHeaders();

            foreach ($headers as $key => $value) {
                $msg .= "\t\t" . $key . " with value " . $value[0] . "\n";
            }
        }

        if ((string) $request->getBody() != '') {
            $msg .= "\thas a body \n";
        }

        $msg .= "\n";

        return $msg;
    }
}
