<?php

namespace PhpPact\Provider;

use Amp\ByteStream\Payload;
use Amp\ByteStream\ResourceOutputStream;
use Amp\Http\Server\Request;
use Amp\Http\Server\RequestHandler\CallableRequestHandler;
use Amp\Http\Server\Response;
use Amp\Http\Server\Server;
use Amp\Http\Status;
use Amp\Log\ConsoleFormatter;
use Amp\Log\StreamHandler;
use Amp\Loop;
use Amp\Process\Process;
use Amp\Socket;
use GuzzleHttp\Psr7\Uri;
use Monolog\Logger;
use PhpPact\Standalone\Installer\Model\Scripts;
use PhpPact\Standalone\ProviderVerifier\Model\VerifierConfigInterface;
use PhpPact\Standalone\ProviderVerifier\Verifier;

class MessageVerifier extends Verifier
{
    /** @var array */
    protected $callbacks;

    /**
     * Default host name for the proxy server
     *
     * @var string
     */
    protected $defaultProxyHost = 'localhost';

    /**
     * Default port for the proxy server to listen on
     *
     * @var int
     */
    protected $defaultProxyPort = 7201;

    /**
     * floor(provider-verification timeout / this value) = default verificationDelaySec
     *
     * @var int
     */
    protected $defaultDelayFactor = 3;

    /**
     * Set the number of seconds to delay the verification test to allow the proxy server to be stood up
     *
     * By default, it is a third of the provider-verification timeout
     *
     * @var float
     */
    protected $verificationDelaySec;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * MessageVerifier constructor.
     *
     * @param VerifierConfigInterface $config
     */
    public function __construct(VerifierConfigInterface $config)
    {
        parent::__construct($config);

        $this->callbacks = [];

        $baseUrl = @$this->config->getProviderBaseUrl();
        if (!$baseUrl) {
            $config->setProviderBaseUrl(new Uri("http://{$this->defaultProxyHost}:{$this->defaultProxyPort}"));
        }

        // default verification delay
        $this->setVerificationDelaySec(\floor($config->getProcessIdleTimeout() / $this->defaultDelayFactor));
    }

    /**
     * @param array $callbacks
     *
     * @return self
     */
    public function setCallbacks(array $callbacks): self
    {
        $this->callbacks = $callbacks;

        return $this;
    }

    /**
     * Add an individual call back
     *
     * @param string   $key
     * @param callable $callback
     *
     * @throws \Exception
     *
     * @return MessageVerifier
     */
    public function addCallback(string $key, callable $callback): self
    {
        if (!isset($this->callbacks[$key])) {
            $this->callbacks[$key] = $callback;
        } else {
            throw new \Exception("Callback with key ($key) already exists");
        }

        return $this;
    }

    /**
     * @param float $verificationDelaySec
     *
     * @return MessageVerifier
     */
    public function setVerificationDelaySec(float $verificationDelaySec): self
    {
        $this->verificationDelaySec = $verificationDelaySec;

        return $this;
    }

    /**
     * @param Logger $logger
     *
     * @return MessageVerifier
     */
    public function setLogger(Logger $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param array $arguments
     *
     * @throws \Exception
     */
    protected function verifyAction(array $arguments)
    {
        if (\count($this->callbacks) < 1) {
            throw new \Exception('Callback needs to bet set when using message pacts');
        }

        $callbacks = $this->callbacks;
        $uri       = $this->config->getProviderBaseUrl();

        $arguments = \array_merge([Scripts::getProviderVerifier()], $arguments);

        /**
         * @throws \Amp\Socket\SocketException
         * @throws \Error
         * @throws \TypeError
         *
         * @return \Generator
         */
        $lambdaLoop = function () use ($callbacks, $arguments, $uri) {
            // spin up a server
            $url     = "{$uri->getHost()}:{$uri->getPort()}";
            $servers = [
                Socket\Server::listen($url)
            ];

            $logger = $this->getLogger();

            $server = new Server($servers, new CallableRequestHandler(function (Request $request) use ($callbacks) {
                if (\count($callbacks) === 1) {
                    $callback = \array_pop($callbacks);
                } else {
                    $payload = new Payload($request->getBody());
                    $requestBody = yield $payload->buffer();
                    $requestBody = \json_decode($requestBody);
                    $description = $requestBody->description;

                    $callback = false;

                    if (isset($this->callbacks[$description])) {
                        $callback = $this->callbacks[$description];
                    }

                    if ($callback === false) {
                        throw new \Exception("Pacts with multiple states need to have callbacks key'ed by the description");
                    }
                }

                //@todo pass $providerStates to the call back
                $out = \call_user_func($callback);

                // return response should only happen if the \call_user_fun()
                return new Response(Status::OK, [
                    'content-type' => 'application/json;',
                ], $out);
            }), $logger);

            yield $server->start();

            // delay long enough for the server to be stood up
            $delay = (int) ($this->verificationDelaySec * 1000);

            // call the provider-verification cmd
            Loop::delay($delay, function () use ($arguments) {
                $cmd = \implode(' ', $arguments);
                $process = new Process($cmd);
                yield $process->start();

                $payload = new Payload($process->getStdout());
                print yield $payload->buffer();

                $code = yield $process->join();

                // if the provider verification cmd returns a non-zero number, the test failed
                if ($code !== 0) {
                    $this->getLogger()->warning(yield $process->getStderr()->read());

                    throw new \Exception("Pact failed to validate.  Exit code: {$code}");
                }

                Loop::stop();
            });
        };

        Loop::run($lambdaLoop);
    }

    /**
     * @return Logger
     */
    private function getLogger()
    {
        if (null === $this->logger) {
            $logHandler = new StreamHandler(new ResourceOutputStream(\STDOUT));
            $logHandler->setFormatter(new ConsoleFormatter(null, null, true));
            $this->logger = new Logger('server');
            $this->logger->pushHandler($logHandler);
        }

        return $this->logger;
    }
}
