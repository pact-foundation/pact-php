<?php

namespace PhpPact\Mocks\MockHttpService\Models;

class ProviderServiceInteraction extends \PhpPact\Models\Interaction implements \JsonSerializable
{
    /**
     * @var \PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest
     */
    private $_request;

    /**
     * @var \PhpPact\Mocks\MockHttpService\Models\ProviderServiceResponse
     */
    private $_response;

    /**
     * @var \PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceRequestMapper
     */
    protected $_providerServiceRequestMapper;

    /**
     * @var \PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceResponseMapper
     */
    protected $_providerServiceResponseMapper;

    public function __construct()
    {
        if (is_callable('parent::__construct')) {
            parent::__construct();
        }
        $this->_providerServiceRequestMapper = new \PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceRequestMapper();
        $this->_providerServiceResponseMapper = new \PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceResponseMapper();
    }

    public function jsonSerialize()
    {
        $obj = parent::jsonSerialize();
        $obj->request = $this->_request;
        $obj->response = $this->_response;

        return $obj;
    }

    /**
     * @return ProviderServiceRequest
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * @return ProviderServiceResponse
     */
    public function getResponse()
    {
        return $this->_response;
    }

    public function setRequest($obj)
    {
        $this->_request = $this->_providerServiceRequestMapper->Convert($obj);
        return $this->_request;
    }

    public function setResponse($obj)
    {
        $this->_response = $this->_providerServiceResponseMapper->Convert($obj);
        return $this->_response;
    }
}
