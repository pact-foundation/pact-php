<?php

namespace PhpPactTest\CompatibilitySuite\Context\V4\Message;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use PhpPact\Consumer\Model\Message;
use PhpPact\Standalone\ProviderVerifier\Model\Config\ProviderTransport;
use PhpPactTest\CompatibilitySuite\Constant\Mismatch;
use PhpPactTest\CompatibilitySuite\Model\Pact\Pact;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Model\Verifier\VerificationError;
use PhpPactTest\CompatibilitySuite\Model\Verifier\VerifierOutput;
use PhpPactTest\CompatibilitySuite\Service\InteractionBuilderInterface;
use PhpPactTest\CompatibilitySuite\Service\InteractionsStorageInterface;
use PhpPactTest\CompatibilitySuite\Service\MessagePactWriterInterface;
use PhpPactTest\CompatibilitySuite\Service\ParserInterface;
use PhpPactTest\CompatibilitySuite\Service\ProviderVerifierInterface;
use PhpPactTest\CompatibilitySuite\Service\ServerInterface;
use PHPUnit\Framework\Assert;

final class ProviderContext implements Context
{
    private int $id = 1;
    private PactPath $pactPath;

    public function __construct(
        private ServerInterface $server,
        private InteractionBuilderInterface $builder,
        private InteractionsStorageInterface $storage,
        private MessagePactWriterInterface $pactWriter,
        private ProviderVerifierInterface $providerVerifier,
        private ParserInterface $parser,
    ) {
        $this->pactPath = new PactPath();
    }

    #[Given('/^a provider is started that can generate the "([^"]+)" message with "(.+)"$/')]
    public function aProviderIsStartedThatCanGenerateTheMessageWith(string $name, string $fixture): void
    {
        $interaction = $this->builder->build([
            'No' => $this->id,
            'description' => sprintf('Interaction for message %s', $name),
            'method' => 'POST',
            'path' => '/messages',
            'body' => 'JSON: ' . json_encode(['description' => $name]),
            'response body' => $fixture,
        ]);
        $this->storage->add(InteractionsStorageInterface::SERVER_DOMAIN, $this->id, $interaction);
        $this->server->register($this->id);
        $this->providerVerifier
            ->getConfig()
                ->addProviderTransport(
                    (new ProviderTransport())
                        ->setProtocol(ProviderTransport::MESSAGE_PROTOCOL)
                        ->setPort($this->server->getPort())
                        ->setPath('/messages')
                        ->setScheme('http')
                );
        ;
    }

    #[Given('a Pact file for :name::fixture is to be verified, but is marked pending')]
    public function aPactFileForIsToBeVerifiedButIsMarkedPending(string $name, string $fixture): void
    {
        $message = new Message();
        $message->setDescription($name);
        $message->setContents($this->parser->parseBody($fixture));
        $this->pactWriter->write($message, $this->pactPath);
        $this->providerVerifier->addSource($this->pactPath);
        $pact = Pact::fromFile($this->pactPath);
        $pact->getInteraction(0)->setPending(true);
        file_put_contents($this->pactPath, $pact->toJson());
    }

    #[Given('a Pact file for :name::fixture is to be verified with the following comments:')]
    public function aPactFileForIsToBeVerifiedWithTheFollowingComments(string $name, string $fixture, TableNode $table): void
    {
        $comments = [];
        foreach ($table->getHash() as $row) {
            switch ($row['type']) {
                case 'text':
                    $comments['text'][] = $row['comment'];
                    break;

                case 'testname':
                    $comments['testname'] = $row['comment'];
                    break;

                default:
                    # code...
                    break;
            }
        }
        $message = new Message();
        $message->setDescription($name);
        $message->setContents($this->parser->parseBody($fixture));
        $this->pactWriter->write($message, $this->pactPath);
        $this->providerVerifier->addSource($this->pactPath);
        $pact = Pact::fromFile($this->pactPath);
        $pact->getInteraction(0)->setComments($comments);
        file_put_contents($this->pactPath, $pact->toJson());
    }

    #[When('the verification is run')]
    public function theVerificationIsRun(): void
    {
        $this->providerVerifier->getConfig()->getProviderInfo()->setPort($this->server->getPort());
        $this->providerVerifier->verify();
    }

    #[Then('the verification will be successful')]
    public function theVerificationWillBeSuccessful(): void
    {
        Assert::assertTrue($this->providerVerifier->getVerifyResult()->isSuccess());
    }

    #[Then('there will be a pending :error error')]
    public function thereWillBeAPendingError(string $error): void
    {
        $output = VerifierOutput::fromJson($this->providerVerifier->getVerifyResult()->getOutput());
        $errors = array_reduce(
            $output->getPendingErrors(),
            function (array $errors, VerificationError $error) {
                switch ($error->getMismatch()->getType()) {
                    case 'error':
                        $errors[] = Mismatch::VERIFIER_MISMATCH_ERROR_MAP[$error->getMismatch()->getMessage()];
                        break;

                    case 'mismatches':
                        foreach ($error->getMismatch()->getMismatches() as $mismatchItem) {
                            $errors[] = Mismatch::VERIFIER_MISMATCH_TYPE_MAP[$mismatchItem->getType()];
                        }
                        break;

                    default:
                        break;
                }

                return $errors;
            },
            []
        );
        Assert::assertContains($error, $errors);
    }

    #[Then('the comment :comment will have been printed to the console')]
    public function theCommentWillHaveBeenPrintedToTheConsole(string $comment): void
    {
        Assert::assertStringContainsString($comment, $this->providerVerifier->getVerifyResult()->getOutput());
    }

    #[Then('the :name will displayed as the original test name')]
    public function theWillDisplayedAsTheOriginalTestName(string $name): void
    {
        Assert::assertStringContainsString(sprintf('Test Name: %s', $name), $this->providerVerifier->getVerifyResult()->getOutput());
    }
}
