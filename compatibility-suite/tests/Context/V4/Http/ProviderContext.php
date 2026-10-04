<?php

namespace PhpPactTest\CompatibilitySuite\Context\V4\Http;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Behat\Step\Given;
use Behat\Step\Then;
use PhpPactTest\CompatibilitySuite\Constant\Mismatch;
use PhpPactTest\CompatibilitySuite\Model\Pact\Pact;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
use PhpPactTest\CompatibilitySuite\Model\Verifier\VerificationError;
use PhpPactTest\CompatibilitySuite\Model\Verifier\VerifierOutput;
use PhpPactTest\CompatibilitySuite\Service\PactWriterInterface;
use PhpPactTest\CompatibilitySuite\Service\ProviderVerifierInterface;
use PHPUnit\Framework\Assert;

final class ProviderContext implements Context
{
    private PactPath $pactPath;

    public function __construct(
        private PactWriterInterface $pactWriter,
        private ProviderVerifierInterface $providerVerifier,
    ) {
        $this->pactPath = new PactPath();
    }

    #[Given('a Pact file for interaction :id is to be verified, but is marked pending')]
    public function aPactFileForInteractionIsToBeVerifiedButIsMarkedPending(int $id): void
    {
        $this->pactWriter->write($id, $this->pactPath);
        $pact = Pact::fromFile($this->pactPath);
        $pact->getInteraction(0)->setPending(true);
        file_put_contents($this->pactPath, $pact->toJson());
        $this->providerVerifier->addSource($this->pactPath);
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

    #[Given('a Pact file for interaction :id is to be verified with the following comments:')]
    public function aPactFileForInteractionIsToBeVerifiedWithTheFollowingComments(int $id, TableNode $table): void
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
        $this->pactWriter->write($id, $this->pactPath);
        $pact = Pact::fromFile($this->pactPath);
        $pact->getInteraction(0)->setComments($comments);
        file_put_contents($this->pactPath, $pact->toJson());
        $this->providerVerifier->addSource($this->pactPath);
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
