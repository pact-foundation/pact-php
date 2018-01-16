<?php

namespace PhpPact\Comparers;

use PHPUnit\Framework\TestCase;

class ComparisonResultTest extends TestCase
{
    public function testFailures()
    {
        // base failures
        $results = new ComparisonResult();
        $results->recordFailure(new DiffComparisonFailure('a', 'b'));
        $results->recordFailure(new ErrorMessageComparisonFailure('Some failure'));

        $this->assertEquals(2, $results->shallowFailureCount(), 'Expect two shallow failures');
        $this->assertEquals(2, \count($results->failures()), 'Expect two deep failures');
        $this->assertTrue($results->hasFailure(), 'Expect failures');

        // child failures with base failures

        $results = new ComparisonResult();
        $results->recordFailure(new DiffComparisonFailure('a', 'b'));
        $results->recordFailure(new ErrorMessageComparisonFailure('Some failure'));

        $childResults = new ComparisonResult();
        $childResults->recordFailure(new ErrorMessageComparisonFailure('Yet another failure'));
        $results->addChildResult($childResults);

        $this->assertEquals(2, $results->shallowFailureCount(), 'Expect two shallow failures');
        $this->assertEquals(3, \count($results->failures()), 'Expect three deep failures');
        $this->assertTrue($results->hasFailure(), 'Expect failures');

        // only child failures
        $results = new ComparisonResult();

        $childResults = new ComparisonResult();
        $childResults->recordFailure(new ErrorMessageComparisonFailure('Yet another failure'));
        $childResults->recordFailure(new DiffComparisonFailure('a', 'b'));
        $results->addChildResult($childResults);

        $this->assertEquals(0, $results->shallowFailureCount(), 'Expect no shallow failures');
        $this->assertEquals(2, \count($results->failures()), 'Expect two deep failures');
        $this->assertTrue($results->hasFailure(), 'Expect failures');

        // try several deep
        $results = new ComparisonResult();
        $results->recordFailure(new DiffComparisonFailure('a', 'b'));

        $childResults = new ComparisonResult();
        $childResults->recordFailure(new ErrorMessageComparisonFailure('Yet another failure'));

        $childChildResults = new ComparisonResult();
        $childChildResults->recordFailure(new ErrorMessageComparisonFailure('My third failure'));

        $childChildChildResults = new ComparisonResult();
        $childChildChildResults->recordFailure(new ErrorMessageComparisonFailure('My third failure'));

        $childChildResults->addChildResult($childChildChildResults);
        $childResults->addChildResult($childChildResults);

        $results->addChildResult($childResults);

        $this->assertEquals(1, $results->shallowFailureCount(), 'Expect one shallow failures');
        $this->assertEquals(4, \count($results->failures()), 'Expect four deep failures');
        $this->assertTrue($results->hasFailure(), 'Expect failures');

        // try several deep with the failure only deep
        $results                = new ComparisonResult();
        $childResults           = new ComparisonResult();
        $childChildResults      = new ComparisonResult();
        $childChildChildResults = new ComparisonResult();

        $childChildChildResults->recordFailure(new ErrorMessageComparisonFailure('My deep first failure'));
        $childChildChildResults->recordFailure(new ErrorMessageComparisonFailure('My deep second failure'));

        $childChildResults->addChildResult($childChildChildResults);
        $childResults->addChildResult($childChildResults);
        $results->addChildResult($childResults);

        $this->assertEquals(0, $results->shallowFailureCount(), 'Expect no shallow failures');
        $this->assertEquals(2, \count($results->failures()), 'Expect two deep failures');
        $this->assertTrue($results->hasFailure(), 'Expect failures');

        // try multiple children in 3rd layer but the first child has failure, testing overwriting
        $results            = new ComparisonResult();
        $childResults       = new ComparisonResult();
        $childChildResults  = new ComparisonResult();
        $childChildResults2 = new ComparisonResult();

        $childChildResults->recordFailure(new ErrorMessageComparisonFailure('My 2nd failure'));
        $childChildResults->recordFailure(new ErrorMessageComparisonFailure('My another failure'));

        $childResults->addChildResult($childChildResults);
        $childResults->addChildResult($childChildResults2);
        $results->addChildResult($childResults);

        $this->assertEquals(0, $results->shallowFailureCount(), 'Expect  shallow failures');
        $this->assertEquals(2, \count($results->failures()), 'Expect two failures');
        $this->assertTrue($results->hasFailure(), 'Expect failures');
    }
}
