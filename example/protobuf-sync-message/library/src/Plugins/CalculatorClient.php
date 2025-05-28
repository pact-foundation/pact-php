<?php
// GENERATED CODE -- DO NOT EDIT!

namespace Plugins;

/**
 */
class CalculatorClient extends \Grpc\BaseStub {

    /**
     * @param string $hostname hostname
     * @param array $opts channel options
     * @param \Grpc\Channel $channel (optional) re-use channel object
     */
    public function __construct($hostname, $opts, $channel = null) {
        parent::__construct($hostname, $opts, $channel);
    }

    /**
     * @param \Plugins\ShapeMessage $argument input argument
     * @param array $metadata metadata
     * @param array $options call options
     * @return \Grpc\UnaryCall
     */
    public function calculate(\Plugins\ShapeMessage $argument,
      $metadata = [], $options = []) {
        return $this->_simpleRequest('/plugins.Calculator/calculate',
        $argument,
        ['\Plugins\AreaResponse', 'decode'],
        $metadata, $options);
    }

}
