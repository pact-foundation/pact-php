<?php

namespace PhpPact;

interface IPactVerifier
{
    /**
     * Define a set up and/or tear down action for a specific state specified by the consumer.
     * This is where you should set up test data, so that you can fulfil the contract outlined by a consumer.
     *
     * @param $providerState the name of the provider state as defined by the consumer interaction, which lives in the Pact file
     * @param $setUp A set up action that will be run before the interaction verify, if the provider has specified it in the interaction. If no action is required please namespace an empty lambda () => {}
     * @param $tearDown A tear down action that will be run after the interaction verify, if the provider has specified it in the interaction. If no action is required please namespace an empty lambda () => {}
     * @param mixed $providerName
     * @param mixed $httpClient
     * @param mixed $httpRequestSender
     */
    public function serviceProvider($providerName, $httpClient, $httpRequestSender);

    public function honoursPactWith($consumerName);

    public function pactUri($uri, $options = null);

    public function verify($description = null, $providerState = null);
}
