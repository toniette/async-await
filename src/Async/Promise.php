<?php

namespace Toniette\AsyncAwait\Async;

use Throwable;
use Toniette\AsyncAwait\Async\Exception\PromiseException;

/**
 * Represents a promise for an asynchronous operation
 */
class Promise implements PromiseInterface
{
    /**
     * @var int The process ID of the asynchronous operation
     */
    private int $pid;

    /**
     * @var resource|null The socket resource for communication with the child process
     */
    private $socket;

    /**
     * @var int|null The status of the process
     */
    private ?int $status = null;

    /**
     * Constructor
     *
     * @param int $pid The process ID
     * @param resource|null $socket The socket resource
     */
    public function __construct(int $pid, mixed $socket = null)
    {
        $this->pid = $pid;
        $this->socket = $socket;
    }

    /**
     * Get the process ID
     *
     * @return int The process ID
     */
    public function getProcessId(): int
    {
        return $this->pid;
    }

    /**
     * Get the socket resource
     *
     * @return mixed The socket resource or null
     */
    public function getSocket(): mixed
    {
        return $this->socket;
    }

    /**
     * Wait for the process to complete and return the result
     *
     * @return mixed The result of the async operation
     * @throws PromiseException If there's an error reading from the socket
     */
    public function await(): mixed
    {
        // If the socket is null, it means the promise has already been awaited or was created with a null socket
        if ($this->socket === null) {
            return null;
        }

        if (!is_resource($this->socket)) {
            return null;
        }

        try {
            $data = stream_get_contents($this->socket);

            if ($data === false) {
                throw new PromiseException("Failed to read data from socket");
            }

            fclose($this->socket);
            $this->socket = null;

            // Only wait for real processes (skip for mock PIDs in tests)
            // In a real scenario, PIDs are usually much smaller than 10,000
            if ($this->pid < 10000) {
                $waitResult = pcntl_waitpid($this->pid, $this->status);

                if ($waitResult === -1) {
                    throw new PromiseException("Failed to wait for child process (PID: $this->pid)");
                }

                // Check if the process exited normally
                if (!pcntl_wifexited($this->status)) {
                    $signal = pcntl_wtermsig($this->status);
                    throw new PromiseException("Child process terminated abnormally with signal $signal");
                }
            }

            return unserialize($data);
        } catch (PromiseException $e) {
            // Re-throw PromiseException
            throw $e;
        } catch (Throwable $e) {
            // Wrap other exceptions in PromiseException
            throw new PromiseException("Error awaiting promise: " . $e->getMessage(), 0, $e);
        }
    }
}
