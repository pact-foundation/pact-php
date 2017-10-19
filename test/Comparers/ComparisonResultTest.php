<?php

namespace PhpPact\Comparers;

use PHPUnit\Framework\TestCase;

class ComparisonResultTest extends TestCase
{
    public function testFailures()
    {
        // base failures
        $results = new \PhpPact\Comparers\ComparisonResult();
        $results->recordFailure(new \PhpPact\Comparers\DiffComparisonFailure("a", "b"));
        $results->recordFailure(new \PhpPact\Comparers\ErrorMessageComparisonFailure("Some failure"));

        $this->assertEquals(2, $results->ShallowFailureCount(), "Expect two shallow failures");
        $this->assertEquals(2, count($results->Failures()), "Expect two deep failures");
        $this->assertTrue($results->HasFailure(), "Expect failures");

        // child failures with base failures

        $results = new \PhpPact\Comparers\ComparisonResult();
        $results->recordFailure(new \PhpPact\Comparers\DiffComparisonFailure("a", "b"));
        $results->recordFailure(new \PhpPact\Comparers\ErrorMessageComparisonFailure("Some failure"));

        $childResults = new \PhpPact\Comparers\ComparisonResult();
        $childResults->recordFailure(new \PhpPact\Comparers\ErrorMessageComparisonFailure("Yet another failure"));
        $results->addChildResult($childResults);

        $this->assertEquals(2, $results->ShallowFailureCount(), "Expect two shallow failures");
        $this->assertEquals(3, count($results->Failures()), "Expect three deep failures");
        $this->assertTrue($results->HasFailure(), "Expect failures");

        // only child failures
        $results = new \PhpPact\Comparers\ComparisonResult();

        $childResults = new \PhpPact\Comparers\ComparisonResult();
        $childResults->recordFailure(new \PhpPact\Comparers\ErrorMessageComparisonFailure("Yet another failure"));
        $childResults->recordFailure(new \PhpPact\Comparers\DiffComparisonFailure("a", "b"));
        $results->addChildResult($childResults);

        $this->assertEquals(0, $results->ShallowFailureCount(), "Expect no shallow failures");
        $this->assertEquals(2, count($results->Failures()), "Expect two deep failures");
        $this->assertTrue($results->HasFailure(), "Expect failures");

        // try several deep
        $results = new \PhpPact\Comparers\ComparisonResult();
        $results->recordFailure(new \PhpPact\Comparers\DiffComparisonFailure("a", "b"));

        $childResults = new \PhpPact\Comparers\ComparisonResult();
        $childResults->recordFailure(new \PhpPact\Comparers\ErrorMessageComparisonFailure("Yet another failure"));

        $childChildResults = new \PhpPact\Comparers\ComparisonResult();
        $childChildResults->recordFailure(new \PhpPact\Comparers\ErrorMessageComparisonFailure("My third failure"));

        $childChildChildResults = new \PhpPact\Comparers\ComparisonResult();
        $childChildChildResults->recordFailure(new \PhpPact\Comparers\ErrorMessageComparisonFailure("My third failure"));

        $childChildResults->addChildResult($childChildChildResults);
        $childResults->addChildResult($childChildResults);

        $results->addChildResult($childResults);

        $this->assertEquals(1, $results->ShallowFailureCount(), "Expect one shallow failures");
        $this->assertEquals(4, count($results->Failures()), "Expect four deep failures");
        $this->assertTrue($results->HasFailure(), "Expect failures");

        // try several deep with the failure only deep
        $results = new \PhpPact\Comparers\ComparisonResult();
        $childResults = new \PhpPact\Comparers\ComparisonResult();
        $childChildResults = new \PhpPact\Comparers\ComparisonResult();
        $childChildChildResults = new \PhpPact\Comparers\ComparisonResult();

        $childChildChildResults->recordFailure(new \PhpPact\Comparers\ErrorMessageComparisonFailure("My deep first failure"));
        $childChildChildResults->recordFailure(new \PhpPact\Comparers\ErrorMessageComparisonFailure("My deep second failure"));

        $childChildResults->addChildResult($childChildChildResults);
        $childResults->addChildResult($childChildResults);
        $results->addChildResult($childResults);

        $this->assertEquals(0, $results->ShallowFailureCount(), "Expect no shallow failures");
        $this->assertEquals(2, count($results->Failures()), "Expect two deep failures");
        $this->assertTrue($results->HasFailure(), "Expect failures");

        // try multiple children in 3rd layer but the first child has failure, testing overwriting
        $results = new \PhpPact\Comparers\ComparisonResult();
        $childResults = new \PhpPact\Comparers\ComparisonResult();
        $childChildResults = new \PhpPact\Comparers\ComparisonResult();
        $childChildResults2 = new \PhpPact\Comparers\ComparisonResult();

        $childChildResults->recordFailure(new \PhpPact\Comparers\ErrorMessageComparisonFailure("My 2nd failure"));
        $childChildResults->recordFailure(new \PhpPact\Comparers\ErrorMessageComparisonFailure("My another failure"));

        $childResults->addChildResult($childChildResults);
        $childResults->addChildResult($childChildResults2);
        $results->addChildResult($childResults);

        $this->assertEquals(0, $results->ShallowFailureCount(), "Expect  shallow failures");
        $this->assertEquals(2, count($results->Failures()), "Expect two failures");
        $this->assertTrue($results->HasFailure(), "Expect failures");
    }
}
