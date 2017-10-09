<?php
/**
 * Created by PhpStorm.
 * User: matr06017
 * Date: 7/20/2017
 * Time: 4:04 PM
 */

namespace PhpPact;

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
    public function PublishFile($file, $version)
    {
        $json = file_get_contents($file);

        if ($json === false) {
            throw new \RuntimeException("Pact file cannot be found: {$file}");
        }

        return $this->PublishJson($json, $version);
    }


    /**
     *
     * @param string $json
     * @param $version
     */
    public function PublishJson($json, $version)
    {
        $jsonDecoded = \json_decode($json, true);
        $mapper = new \PhpPact\Mocks\MockHttpService\Mappers\ProviderServicePactMapper();
        $pact = $mapper->Convert($jsonDecoded);
        return $this->Publish($pact, $version);
    }


    /**
     * @param Mocks\MockHttpService\Models\ProviderServicePactFile $pact
     * @param $version
     *
     * @return bool return true if response was 200
     */
    public function Publish(\PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile $pact, $version)
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
    public function RetrieveLatestProviderPacts($providerName)
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

        $obj = \json_decode($body, true);

        $pacts = array();

        if (isset($obj['_links']) && count($obj['_links']['pacts']) > 0) {
            foreach ($obj['_links']['pacts'] as $pactLink) {
                $consumerName = $pactLink['name'];
                $version = $this->ExtractVersionFromUrl($pactLink['href']);

                $pact = $this->RetrievePact($providerName, $consumerName, $version);
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
     * @param string $version
     * @return Mocks\MockHttpService\Models\ProviderServicePactFile
     */
    public function RetrievePact($providerName, $consumerName, $version = "latest")
    {

        $url = $this->_uriOptions->getBaseUri();
        $path = '/pacts/provider/' . urlencode($providerName) . '/consumer/' . urlencode($consumerName);

        if (strtolower($version) == "latest") {
            $path .= "/" . $version;
        } else {
            $path .= '/version/' . $version;
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
        $body = (string)$httpResponse->getBody();

        // map to pact object
        $mapper = new \PhpPact\Mocks\MockHttpService\Mappers\ProviderServicePactMapper();
        $pact = $mapper->Convert($body);

        return $pact;
    }

    /**
     * Used to walk HAL links
     *
     * @param $url
     * @return mixed
     */
    private function ExtractVersionFromUrl($url)
    {
        $arr = explode('/', $url);
        $index = count($arr) - 1;
        return $arr[$index];
    }
}
