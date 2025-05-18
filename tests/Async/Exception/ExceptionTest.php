<?php

namespace Toniette\AsyncAwait\Tests\Async\Exception;

use PHPUnit\Framework\TestCase;
use Toniette\AsyncAwait\Async\Exception\AsyncException;
use Toniette\AsyncAwait\Async\Exception\ProcessException;
use Toniette\AsyncAwait\Async\Exception\PromiseException;
use Toniette\AsyncAwait\Async\Exception\SocketException;

class ExceptionTest extends TestCase
{
    /**
     * Test that AsyncException can be instantiated and used
     */
    public function testAsyncException()
    {
        $exception = new AsyncException("Test message");
        $this->assertInstanceOf(AsyncException::class, $exception);
        $this->assertEquals("Test message", $exception->getMessage());
    }

    /**
     * Test that ProcessException can be instantiated and used
     */
    public function testProcessException()
    {
        $exception = new ProcessException("Test message");
        $this->assertInstanceOf(ProcessException::class, $exception);
        $this->assertInstanceOf(AsyncException::class, $exception);
        $this->assertEquals("Test message", $exception->getMessage());
    }

    /**
     * Test that SocketException can be instantiated and used
     */
    public function testSocketException()
    {
        $exception = new SocketException("Test message");
        $this->assertInstanceOf(SocketException::class, $exception);
        $this->assertInstanceOf(AsyncException::class, $exception);
        $this->assertEquals("Test message", $exception->getMessage());
    }

    /**
     * Test that PromiseException can be instantiated and used
     */
    public function testPromiseException()
    {
        $exception = new PromiseException("Test message");
        $this->assertInstanceOf(PromiseException::class, $exception);
        $this->assertInstanceOf(AsyncException::class, $exception);
        $this->assertEquals("Test message", $exception->getMessage());
    }

    /**
     * Test that exceptions can be caught by their parent class
     */
    public function testExceptionHierarchy()
    {
        $caught = false;

        try {
            throw new PromiseException("Test exception");
        } catch (AsyncException $e) {
            $caught = true;
            $this->assertEquals("Test exception", $e->getMessage());
        }

        $this->assertTrue($caught, "Exception should be caught by parent class");
    }

    /**
     * Test that exceptions can include a previous exception
     */
    public function testExceptionWithPrevious()
    {
        $previous = new \Exception("Previous exception");
        $exception = new PromiseException("Test message", 0, $previous);

        $this->assertEquals("Test message", $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }
}