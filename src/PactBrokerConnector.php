<?php

namespace PhpPact;

use \PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile;
use \PhpPact\Mocks\MockHttpService\Mappers\ProviderServicePactMapper;

class PactBrokerConnector
{
    private $_uriOptions;

    public function __construct($uriOptions = null)
    {
        if ($uriOptions) {
            if (!($uriOptions instanceof \PhpPact\PactUriOptions)) {
                throw new \RuntimeException(sprintf("Options need to be \PhpPact\PactUriOptions, not %s", get_class($uriOptions)));
            } else {
                $this->_uriOptions = $uriOptions;
            }
        }
    }

    /**
     * @return null|PactUriOptions
     */
    public function getUriOptions()
    {
        return $this->_uriOptions;
    }

    /**
     * @param null|PactUriOptions $uriOptions
     * @return PactBrokerConnector
     */
    public function setUriOptions($uriOptions)
    {
        $this->_uriOptions = $uriOptions;
        return $this;
    }


    /**
     * Read a file and post it appropriately.
     *
     * @param $file - file location of pact
     * @param $version - version of pact
     */
    public function publishFile($file, $version)
    {
        $json = file_get_contents($file);

        if ($json === false) {
            throw new \RuntimeException("Pact file cannot be found: {$file}");
        }

        return $this->publishJson($json, $version);
    }


    /**
     *
     * @param string $json
     * @param $version
     */
    public function publishJson($json, $version)
    {
        $jsonDecoded = \json_decode($json);
        $mapper = new ProviderServicePactMapper();
        $pact = $mapper->convert($jsonDecoded);
        return $this->publish($pact, $version);
    }


    /**
     * @param Mocks\MockHttpService\Models\ProviderServicePactFile $pact
     * @param $version
     *
     * @return bool return true if response was 200
     */
    public function publish(ProviderServicePactFile $pact, $version)
    {
        if (!isset($this->_uriOptions)) {
            throw new \RuntimeException("Options is not set and needs to be \PhpPact\PactUriOptions.");
        }

        /*
            curl -v -XPUT -H "Content-Type: application/json" -d@spec/pacts/a_consumer-a_provider.json http://your-pact-broker/pacts/provider/A%20Provider/consumer/A%20Consumer/version/1.0.0
        */

        $url = $this->_uriOptions->getBaseUri();
        $path = '/pacts/provider/' . urlencode($pact->getProvider()->getName()) . '/consumer/' . urlencode($pact->getConsumer()->getName()) . '/version/' . $version;
        $body = \json_encode($pact);

        // build request
        $uri = (new \Windwalker\Uri\PsrUri($url))
            ->withPath($path);

        $httpRequest = (new \Windwalker\Http\Request\Request())
            ->withUri($uri)
            ->withAddedHeader("Content-Type", "application/json")
            ->withMethod("put");

        if ($this->_uriOptions->getUsername() && $this->_uriOptions->getPassword()) {
            $httpRequest = $httpRequest->withAddedHeader("authorization", $this->_uriOptions->AuthorizationHeader());
        }

        $httpRequest->getBody()->write($body);

        // send request
        $httpClient = new \Windwalker\Http\HttpClient();
        $httpResponse = $httpClient->sendRequest($httpRequest);
        $statusCode = intval($httpResponse->getStatusCode());

        if ($statusCode == 200) {
            return true;
        }

        return false;
    }


    /**
     * Integrate with PactBroker to retrieve all packs for a particular provider
     *
     * http://{your-pact-broker}/pacts/provider/{your-provider}/latest
     *
     * @param $providerName
     * @return array
     */
    public function retrieveLatestProviderPacts($providerName)
    {
        $url = $this->_uriOptions->getBaseUri();
        $path = '/pacts/provider/' . urlencode($providerName) . '/latest';

        // build request
        $uri = (new \Windwalker\Uri\PsrUri($url))
            ->withPath($path);

        $httpRequest = (new \Windwalker\Http\Request\Request())
            ->withUri($uri)
            ->withMethod("GET");

        if ($this->_uriOptions->getUsername() && $this->_uriOptions->getPassword()) {
            $httpRequest = $httpRequest->withAddedHeader("authorization", $this->_uriOptions->AuthorizationHeader());
        }

        // send the request
        $httpClient = new \Windwalker\Http\HttpClient();
        $httpResponse = $httpClient->sendRequest($httpRequest);
        $body = (string)$httpResponse->getBody();

        $obj = \json_decode($body);

        $pacts = array();

        if (isset($obj->_links) && count($obj->_links->pacts) > 0) {
            foreach ($obj->_links->pacts as $pactLink) {
                $consumerName = $pactLink->name;
                $version = $this->extractVersionFromUrl($pactLink->href);

                $pact = $this->retrievePact($providerName, $consumerName, $version);
                $pacts[] = $pact;
            }
        }

        return $pacts;
    }

    /**
     * Integrate with PactBroker to retrieve particular pact
     *
     * http://{your-pact-broker}/pacts/provider/{your-provider}/consumer/{your-consumer}/version/{your-version}
     *
     * @param $providerName
     * @param $consumerName
     * @param string $consumerVersion
     * @return Mocks\MockHttpService\Models\ProviderServicePactFile
     */
    public function retrievePact($providerName, $consumerName, $consumerVersion = "latest")
    {
        if (!isset($this->_uriOptions)) {
            throw new \RuntimeException("Options is not set and needs to be \PhpPact\PactUriOptions.");
        }

        $httpResponse = $this->sendHttpRequestForPact($providerName, $consumerName, $consumerVersion);
        $body = (string)$httpResponse->getBody();

        // map to pact object
        $mapper = new ProviderServicePactMapper();
        $pact = $mapper->convert($body);

        return $pact;
    }

    /**
     * Used to walk HAL links
     *
     * @param $url
     * @return mixed
     */
    private function extractVersionFromUrl($url)
    {
        $arr = explode('/', $url);
        $index = count($arr) - 1;
        return $arr[$index];
    }

    /**
     *
     * @param $providerName
     * @param $consumerName
     * @param string $consumerVersion
     * @return mixed
     */
    private function sendHttpRequestForPact($providerName, $consumerName, $consumerVersion = "latest") {
        $url = $this->_uriOptions->getBaseUri();
        $path = '/pacts/provider/' . urlencode($providerName) . '/consumer/' . urlencode($consumerName);

        if (strtolower($consumerVersion) == "latest") {
            $path .= "/" . $consumerVersion;
        } else {
            $path .= '/version/' . $consumerVersion;
        }

        // build request
        $uri = (new \Windwalker\Uri\PsrUri($url))
            ->withPath($path);

        $httpRequest = (new \Windwalker\Http\Request\Request())
            ->withUri($uri)
            ->withMethod("GET");

        if ($this->_uriOptions->getUsername() && $this->_uriOptions->getPassword()) {
            $httpRequest = $httpRequest->withAddedHeader("authorization", $this->_uriOptions->AuthorizationHeader());
        }

        // send the request
        $httpClient = new \Windwalker\Http\HttpClient();
        $httpResponse = $httpClient->sendRequest($httpRequest);

        return $httpResponse;
    }

    /**
     * Follow the HAL links to publish verification results of a Provider pact with a Consumer
     *
     * @param bool $verificationState
     * @param string $buildUrl
     * @param string $providerName
     * @param string $providerVersion
     * @param string $consumerName
     * @param string $consumerVersion
     * @return bool
     */
    public function verify(bool $verificationState,  $buildUrl, $providerName, $providerVersion, $consumerName, $consumerVersion = 'latest')
    {
        if (!isset($this->_uriOptions)) {
            throw new \RuntimeException("Options is not set and needs to be \PhpPact\PactUriOptions.");
        }

        $httpResponse = $this->sendHttpRequestForPact($providerName, $consumerName, $consumerVersion);
        $body = (string)$httpResponse->getBody();

        // extract HAL link to post verification
        $jsonBody = \json_decode($body);
        if (!isset($jsonBody->_links->{"pb:publish-verification-results"}->href)) {
            throw new \RuntimeException("Unable to find HAL link to publish verification results.");
        }

        // results to publish
        $results = new \stdClass();
        $results->success = $verificationState;
        $results->providerApplicationVersion = $providerVersion;
        $results->buildUrl = $buildUrl;


        // build request
        $fullUrl = $jsonBody->_links->{"pb:publish-verification-results"}->href;
        $urlParts = parse_url($fullUrl);
        $url = $urlParts['scheme'] . '://' . $urlParts['host'];
        $path = $urlParts['path'];

        $uri = (new \Windwalker\Uri\PsrUri($url))
            ->withPath($path);

        $httpRequest = (new \Windwalker\Http\Request\Request())
            ->withUri($uri)
            ->withAddedHeader("Content-Type", "application/json")
            ->withMethod("POST");

        if ($this->_uriOptions->getUsername() && $this->_uriOptions->getPassword()) {
            $httpRequest = $httpRequest->withAddedHeader("authorization", $this->_uriOptions->AuthorizationHeader());
        }

        $httpRequest->getBody()->write(\json_encode($results));

        // send request
        $httpClient = new \Windwalker\Http\HttpClient();
        $httpResponse = $httpClient->sendRequest($httpRequest);
        $statusCode = intval($httpResponse->getStatusCode());

        if ($statusCode == 200) {
            return true;
        }

        return false;
    }
}
