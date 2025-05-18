<?php

namespace Toniette\AsyncAwait\Tests\Async;

use PHPUnit\Framework\TestCase;
use Toniette\AsyncAwait\Async\AsyncManager;
use Toniette\AsyncAwait\Async\Promise;
use Toniette\AsyncAwait\Async\PromiseInterface;

class AsyncManagerTest extends TestCase
{
    /**
     * @var AsyncManager
     */
    private AsyncManager $asyncManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->asyncManager = new AsyncManager();
    }

    /**
     * Test that async() returns a Promise object
     */
    public function testAsyncReturnsPromise()
    {
        // Skip this test if the pcntl extension is not available
        if (!extension_loaded('pcntl')) {
            $this->markTestSkipped('PCNTL extension is not available');
        }

        $fn = function() {
            return 'test';
        };

        $promise = $this->asyncManager->async($fn);

        $this->assertInstanceOf(PromiseInterface::class, $promise);
        $this->assertInstanceOf(Promise::class, $promise);

        // Clean up by awaiting the promise
        $this->asyncManager->await($promise);
    }

    /**
     * Test that async() executes the function and returns the result via await()
     */
    public function testAsyncExecutesFunctionAndAwaitReturnsResult()
    {
        // Skip this test if pcntl extension is not available
        if (!extension_loaded('pcntl')) {
            $this->markTestSkipped('PCNTL extension is not available');
        }

        $fn = function(string $message) {
            return "Hello, $message!";
        };

        $promise = $this->asyncManager->async($fn, "World");
        $result = $this->asyncManager->await($promise);

        $this->assertEquals("Hello, World!", $result);
    }

    /**
     * Test that pool() waits for all promises and returns their results
     */
    public function testPoolWaitsForAllPromisesAndReturnsResults()
    {
        // Skip this test if pcntl extension is not available
        if (!extension_loaded('pcntl')) {
            $this->markTestSkipped('PCNTL extension is not available');
        }

        $fn = function(int $value) {
            return $value * 2;
        };

        $promise1 = $this->asyncManager->async($fn, 1);
        $promise2 = $this->asyncManager->async($fn, 2);
        $promise3 = $this->asyncManager->async($fn, 3);

        $results = $this->asyncManager->pool(null, $promise1, $promise2, $promise3);

        $this->assertCount(3, $results);
        $this->assertEquals([2, 4, 6], $results);
    }

    /**
     * Test that pool() calls the callback function with the results
     */
    public function testPoolCallsCallbackWithResults()
    {
        // Skip this test if pcntl extension is not available
        if (!extension_loaded('pcntl')) {
            $this->markTestSkipped('PCNTL extension is not available');
        }

        $fn = function(int $value) {
            return $value * 2;
        };

        $promise1 = $this->asyncManager->async($fn, 1);
        $promise2 = $this->asyncManager->async($fn, 2);

        $callbackCalled = false;
        $callbackResults = [];

        $callback = function(int ...$results) use (&$callbackCalled, &$callbackResults) {
            $callbackCalled = true;
            $callbackResults = $results;
        };

        $results = $this->asyncManager->pool($callback, $promise1, $promise2);

        $this->assertTrue($callbackCalled);
        $this->assertEquals([2, 4], $callbackResults);
        $this->assertEquals([2, 4], $results);
    }

    /**
     * Test that async() throws an exception when there's an error forking the process
     */
    public function testAsyncThrowsExceptionOnForkError()
    {
        // This test is difficult to implement because we can't easily mock pcntl_fork.
        // We'll skip it for now
        $this->markTestSkipped('Cannot easily test fork errors');
    }

    /**
     * Test that async() can handle multiple parameters
     */
    public function testAsyncHandlesMultipleParameters()
    {
        // Skip this test if pcntl extension is not available
        if (!extension_loaded('pcntl')) {
            $this->markTestSkipped('PCNTL extension is not available');
        }

        $fn = function(string $a, string $b, string $c) {
            return "$a $b $c";
        };

        $promise = $this->asyncManager->async($fn, "Hello", "beautiful", "world");
        $result = $this->asyncManager->await($promise);

        $this->assertEquals("Hello beautiful world", $result);
    }

    /**
     * Test that async() can handle complex data types
     */
    public function testAsyncHandlesComplexDataTypes()
    {
        // Skip this test if pcntl extension is not available
        if (!extension_loaded('pcntl')) {
            $this->markTestSkipped('PCNTL extension is not available');
        }

        $fn = function(array $data) {
            return array_sum($data);
        };

        $promise = $this->asyncManager->async($fn, [1, 2, 3, 4, 5]);
        $result = $this->asyncManager->await($promise);

        $this->assertEquals(15, $result);
    }
}