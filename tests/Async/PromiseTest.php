<?php

namespace Toniette\AsyncAwait\Tests\Async;

use PHPUnit\Framework\TestCase;
use Toniette\AsyncAwait\Async\Promise;

class PromiseTest extends TestCase
{
    /**
     * Test that the Promise constructor sets the process ID and socket correctly
     */
    public function testConstructor()
    {
        // Create a socket pair for testing
        $sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
        $this->assertIsArray($sockets);
        $this->assertCount(2, $sockets);

        $pid = 12345; // Mock process ID
        $promise = new Promise($pid, $sockets[0]);

        $this->assertEquals($pid, $promise->getProcessId());
        $this->assertSame($sockets[0], $promise->getSocket());

        // Clean up
        fclose($sockets[0]);
        fclose($sockets[1]);
    }

    /**
     * Test that await returns null when the socket is not valid
     */
    public function testAwaitWithInvalidSocket()
    {
        $pid = 12345; // Mock process ID
        $promise = new Promise($pid, null);

        $this->assertNull($promise->await());
    }

    /**
     * Test that await returns the unserialized data from the socket
     */
    public function testAwaitWithValidSocket()
    {
        // Skip this test if pcntl extension is not available
        if (!extension_loaded('pcntl')) {
            $this->markTestSkipped('PCNTL extension is not available');
        }

        // Create a real process to test await
        $sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);

        $pid = pcntl_fork();

        if ($pid === -1) {
            $this->fail('Failed to fork process');
        } elseif ($pid === 0) {
            // Child process
            fclose($sockets[0]);

            // Send a serialized value through the socket
            $data = 'Test result';
            fwrite($sockets[1], serialize($data));
            fclose($sockets[1]);

            // Exit the child process
            exit(0);
        } else {
            // Parent process
            fclose($sockets[1]);

            $promise = new Promise($pid, $sockets[0]);

            // Test that await returns the unserialized data
            $result = $promise->await();
            $this->assertEquals('Test result', $result);
        }
    }

    /**
     * Test that the socket is closed after await
     */
    public function testSocketIsClosedAfterAwait()
    {
        // Create a socket pair for testing
        $sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);

        // Write some data to the socket
        fwrite($sockets[1], serialize('Test data'));
        fclose($sockets[1]);

        $pid = 12345; // Mock process ID
        $promise = new Promise($pid, $sockets[0]);

        // Mock pcntl_waitpid to avoid waiting for a real process
        if (function_exists('pcntl_waitpid')) {
            // We can't easily mock built-in functions, so we'll just call it with a non-existent PID
            // This will return -1 and not block
        }

        // Call await
        $promise->await();

        // Test that the socket is closed (getSocket should return null)
        $this->assertNull($promise->getSocket());
    }
}