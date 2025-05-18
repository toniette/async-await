<?php

namespace Toniette\AsyncAwait\Async;

use Toniette\AsyncAwait\Async\Exception\PromiseException;

/**
 * Interface for Promise objects that represent asynchronous operations
 */
interface PromiseInterface
{
    /**
     * Get the process ID of the asynchronous operation
     *
     * @return int The process ID
     */
    public function getProcessId(): int;

    /**
     * Wait for the process to complete and return the result
     *
     * @return mixed The result of the async operation, or null if the promise has already been awaited
     * @throws PromiseException If there's an error reading from the socket
     */
    public function await(): mixed;

    /**
     * Get the socket resource associated with this promise
     *
     * @return mixed The socket resource or null
     */
    public function getSocket(): mixed;
}
