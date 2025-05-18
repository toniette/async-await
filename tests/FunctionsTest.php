<?php

namespace Toniette\AsyncAwait\Tests;

use PHPUnit\Framework\TestCase;
use Toniette\AsyncAwait\Async\PromiseInterface;

class FunctionsTest extends TestCase
{
    /**
     * Test that async() returns a Promise
     */
    public function testAsyncReturnsPromise()
    {
        // Skip this test if pcntl extension is not available
        if (!extension_loaded('pcntl')) {
            $this->markTestSkipped('PCNTL extension is not available');
        }

        $fn = function() {
            return 'test';
        };

        $promise = async($fn);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        // Clean up by awaiting the promise
        await($promise);
    }

    /**
     * Test that async() and await() work together
     */
    public function testAsyncAndAwaitWorkTogether()
    {
        // Skip this test if pcntl extension is not available
        if (!extension_loaded('pcntl')) {
            $this->markTestSkipped('PCNTL extension is not available');
        }

        $fn = function(string $message) {
            return "Hello, $message!";
        };

        $promise = async($fn, "World");
        $result = await($promise);

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

        $promise1 = async($fn, 1);
        $promise2 = async($fn, 2);
        $promise3 = async($fn, 3);

        $results = pool(null, $promise1, $promise2, $promise3);

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

        $promise1 = async($fn, 1);
        $promise2 = async($fn, 2);

        $callbackCalled = false;
        $callbackResults = [];

        $callback = function(int ...$results) use (&$callbackCalled, &$callbackResults) {
            $callbackCalled = true;
            $callbackResults = $results;
        };

        $results = pool($callback, $promise1, $promise2);

        $this->assertTrue($callbackCalled);
        $this->assertEquals([2, 4], $callbackResults);
        $this->assertEquals([2, 4], $results);
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

        $promise = async($fn, "Hello", "beautiful", "world");
        $result = await($promise);

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

        $promise = async($fn, [1, 2, 3, 4, 5]);
        $result = await($promise);

        $this->assertEquals(15, $result);
    }
}