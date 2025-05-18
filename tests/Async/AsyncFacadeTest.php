<?php

namespace Toniette\AsyncAwait\Tests\Async;

use PHPUnit\Framework\TestCase;
use Toniette\AsyncAwait\Async\AsyncFacade;
use Toniette\AsyncAwait\Async\PromiseInterface;

class AsyncFacadeTest extends TestCase
{
    /**
     * Test that AsyncFacade::async() returns a Promise
     */
    public function testAsyncReturnsPromise()
    {
        // Skip this test if pcntl extension is not available
        if (!extension_loaded('pcntl')) {
            $this->markTestSkipped('PCNTL extension is not available');
        }

        $fn = function () {
            return 'test';
        };

        $promise = AsyncFacade::async($fn);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        // Clean up by awaiting the promise
        AsyncFacade::await($promise);
    }

    /**
     * Test that AsyncFacade::async() and AsyncFacade::await() work together
     */
    public function testAsyncAndAwaitWorkTogether()
    {
        // Skip this test if pcntl extension is not available
        if (!extension_loaded('pcntl')) {
            $this->markTestSkipped('PCNTL extension is not available');
        }

        $fn = function (string $message) {
            return "Hello, $message!";
        };

        $promise = AsyncFacade::async($fn, "World");
        $result = AsyncFacade::await($promise);

        $this->assertEquals("Hello, World!", $result);
    }

    /**
     * Test that AsyncFacade::pool() waits for all promises and returns their results
     */
    public function testPoolWaitsForAllPromisesAndReturnsResults()
    {
        // Skip this test if pcntl extension is not available
        if (!extension_loaded('pcntl')) {
            $this->markTestSkipped('PCNTL extension is not available');
        }

        $fn = function (int $value) {
            return $value * 2;
        };

        $promise1 = AsyncFacade::async($fn, 1);
        $promise2 = AsyncFacade::async($fn, 2);
        $promise3 = AsyncFacade::async($fn, 3);

        $results = AsyncFacade::pool(null, $promise1, $promise2, $promise3);

        $this->assertCount(3, $results);
        $this->assertEquals([2, 4, 6], $results);
    }

    /**
     * Test that AsyncFacade::pool() calls the callback function with the results
     */
    public function testPoolCallsCallbackWithResults()
    {
        // Skip this test if pcntl extension is not available
        if (!extension_loaded('pcntl')) {
            $this->markTestSkipped('PCNTL extension is not available');
        }

        $fn = function (int $value) {
            return $value * 2;
        };

        $promise1 = AsyncFacade::async($fn, 1);
        $promise2 = AsyncFacade::async($fn, 2);

        $callbackCalled = false;
        $callbackResults = [];

        $callback = function (int ...$results) use (&$callbackCalled, &$callbackResults) {
            $callbackCalled = true;
            $callbackResults = $results;
        };

        $results = AsyncFacade::pool($callback, $promise1, $promise2);

        $this->assertTrue($callbackCalled);
        $this->assertEquals([2, 4], $callbackResults);
        $this->assertEquals([2, 4], $results);
    }

    /**
     * Test that AsyncFacade maintains a singleton instance of AsyncManager
     *
     * Note: This test is a bit of a hack because we can't easily test private static properties.
     * We're inferring that the same instance is used by checking that multiple calls to async()
     * don't cause issues.
     */
    public function testAsyncFacadeMaintainsSingletonInstance()
    {
        // Skip this test if pcntl extension is not available
        if (!extension_loaded('pcntl')) {
            $this->markTestSkipped('PCNTL extension is not available');
        }

        $fn = function () {
            return 'test';
        };

        // Make multiple calls to async() which should use the same AsyncManager instance
        $promise1 = AsyncFacade::async($fn);
        $promise2 = AsyncFacade::async($fn);

        // If they were different instances, we might see issues, but we're just checking
        // that we can get results from both promises
        $result1 = AsyncFacade::await($promise1);
        $result2 = AsyncFacade::await($promise2);

        $this->assertEquals('test', $result1);
        $this->assertEquals('test', $result2);
    }
}