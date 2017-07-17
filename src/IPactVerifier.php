<?php
namespace PhpPact;

interface IPactVerifier
{

    /**
     * Define a set up and/or tear down action for a specific state specified by the consumer.
     * This is where you should set up test data, so that you can fulfil the contract outlined by a consumer.
     *
     * @param $providerState The name of the provider state as defined by the consumer interaction, which lives in the Pact file.
     * @param $setUp A set up action that will be run before the interaction verify, if the provider has specified it in the interaction. If no action is required please namespace an empty lambda () => {}
     * @param $tearDown A tear down action that will be run after the interaction verify, if the provider has specified it in the interaction. If no action is required please namespace an empty lambda () => {}
     */

    function ServiceProvider($providerName, $httpClient, $httpRequestSender);

    function HonoursPactWith($consumerName);

    function PactUri($uri, $options = null);

    function Verify($description = null, $providerState = null);
}