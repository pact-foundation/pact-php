<?php

namespace PhpPactTest\CompatibilitySuite\Context\V4\Http;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use PhpPactTest\CompatibilitySuite\Constant\Mismatch;
use PhpPactTest\CompatibilitySuite\Model\PactPath;
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

    /**
     * @Given a Pact file for interaction :id is to be verified, but is marked pending
     */
    public function aPactFileForInteractionIsToBeVerifiedButIsMarkedPending(int $id): void
    {
        $this->pactWriter->write($id, $this->pactPath);
        $pact = json_decode(file_get_contents($this->pactPath), true);
        $pact['interactions'][0]['pending'] = true;
        file_put_contents($this->pactPath, json_encode($pact));
        $this->providerVerifier->addSource($this->pactPath);
    }

    /**
     * @Then there will be a pending :error error
     */
    public function thereWillBeAPendingError(string $error): void
    {
        $output = json_decode($this->providerVerifier->getVerifyResult()->getOutput(), true);
        $errors = array_reduce(
            $output['pendingErrors'],
            function (array $errors, array $error) {
                switch ($error['mismatch']['type']) {
                    case 'error':
                        $errors[] = Mismatch::VERIFIER_MISMATCH_ERROR_MAP[$error['mismatch']['message']];
                        break;

                    case 'mismatches':
                        foreach ($error['mismatch']['mismatches'] as $mismatch) {
                            $errors[] = Mismatch::VERIFIER_MISMATCH_TYPE_MAP[$mismatch['type']];
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

    /**
     * @Given a Pact file for interaction :id is to be verified with the following comments:
     */
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
        $pact = json_decode(file_get_contents($this->pactPath), true);
        $pact['interactions'][0]['comments'] = $comments;
        file_put_contents($this->pactPath, json_encode($pact));
        $this->providerVerifier->addSource($this->pactPath);
    }

    /**
     * @Then the comment :comment will have been printed to the console
     */
    public function theCommentWillHaveBeenPrintedToTheConsole(string $comment): void
    {
        Assert::assertStringContainsString($comment, $this->providerVerifier->getVerifyResult()->getOutput());
    }

    /**
     * @Then the :name will displayed as the original test name
     */
    public function theWillDisplayedAsTheOriginalTestName(string $name): void
    {
        Assert::assertStringContainsString(sprintf('Test Name: %s', $name), $this->providerVerifier->getVerifyResult()->getOutput());
    }
}
