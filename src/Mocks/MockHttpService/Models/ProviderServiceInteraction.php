<?php

namespace PhpPact\Mocks\MockHttpService\Models;

use PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceRequestMapper;
use PhpPact\Mocks\MockHttpService\Mappers\ProviderServiceResponseMapper;

class ProviderServiceInteraction extends \PhpPact\Models\Interaction implements \JsonSerializable
{
    /**
     * @var ProviderServiceRequest
     */
    private $_request;

    /**
     * @var ProviderServiceResponse
     */
    private $_response;

    /**
     * @var ProviderServiceRequestMapper
     */
    protected $_providerServiceRequestMapper;

    /**
     * @var ProviderServiceResponseMapper
     */
    protected $_providerServiceResponseMapper;

    public function __construct()
    {
        if (is_callable('parent::__construct')) {
            parent::__construct();
        }
        $this->_providerServiceRequestMapper = new ProviderServiceRequestMapper();
        $this->_providerServiceResponseMapper = new ProviderServiceResponseMapper();
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
        $this->_request = $this->_providerServiceRequestMapper->convert($obj);
        return $this->_request;
    }

    public function setResponse($obj)
    {
        $this->_response = $this->_providerServiceResponseMapper->convert($obj);
        return $this->_response;
    }
}
