<?php

namespace Toniette\AsyncAwait\Async;

use Toniette\AsyncAwait\Async\Exception\ProcessException;
use Toniette\AsyncAwait\Async\Exception\PromiseException;
use Toniette\AsyncAwait\Async\Exception\SocketException;

/**
 * Manages asynchronous operations
 */
class AsyncManager
{
    /**
     * Execute a function asynchronously in a separate process
     *
     * @param callable $fn The function to execute asynchronously
     * @param mixed ...$params Parameters to pass to the function
     * @return PromiseInterface A Promise object representing the asynchronous operation
     * @throws SocketException If there's an error, setting up sockets
     * @throws ProcessException If there's an error, forking the process
     */
    public function async(callable $fn, mixed ...$params): PromiseInterface
    {
        $sockets = $this->createSocketPair();
        $pid = $this->forkProcess($fn, $params, $sockets);

        // Parent process
        if (is_resource($sockets[1])) {
            fclose($sockets[1]);
        }

        return new Promise($pid, $sockets[0]);
    }

    /**
     * Wait for a promise to complete and return its result
     *
     * @param PromiseInterface $promise The promise to wait for
     * @return mixed The result of the asynchronous operation
     * @throws PromiseException
     */
    public function await(PromiseInterface $promise): mixed
    {
        return $promise->await();
    }

    /**
     * Wait for multiple promises to complete and optionally process their results
     *
     * @param callable|null $fn Optional callback function to process results
     * @param PromiseInterface ...$promises List of Promise objects to wait for
     * @return array Array of results from all promises
     */
    public function pool(?callable $fn, PromiseInterface ...$promises): array
    {
        $sockets = [];
        $receivedResults = [];

        foreach ($promises as $index => $promise) {
            $socket = $promise->getSocket();
            if ($socket && is_resource($socket)) {
                $sockets[$index] = $socket;
            }
        }

        while (!empty($sockets)) {
            $read = $sockets;
            $write = null;
            $except = null;

            if (stream_select($read, $write, $except, 0, 500000) > 0) {
                foreach ($read as $index => $socket) {
                    if (!is_resource($socket)) {
                        unset($sockets[$index]);
                        continue;
                    }

                    $data = stream_get_contents($socket);
                    fclose($socket);
                    unset($sockets[$index]);

                    pcntl_waitpid($promises[$index]->getProcessId(), $status);

                    $receivedResults[] = ['index' => $index, 'result' => unserialize($data)];
                }
            }
        }

        usort($receivedResults, function (array $a, array $b): int {
            return $a['index'] <=> $b['index'];
        });

        $results = array_column($receivedResults, 'result');

        if ($fn !== null) {
            call_user_func($fn, ...$results);
        }

        return $results;
    }

    /**
     * Create a socket pair for inter-process communication
     *
     * @return array The created socket pair
     * @throws SocketException If there's an error creating the socket pair
     */
    private function createSocketPair(): array
    {
        $sockets = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);

        if ($sockets === false) {
            throw new SocketException("Failed to create socket pair for inter-process communication");
        }

        return $sockets;
    }

    /**
     * Fork the process to execute the function asynchronously
     *
     * @param callable $fn The function to execute
     * @param array $params The parameters to pass to the function
     * @param array $sockets The socket pair for communication
     * @return int The process ID of the child process
     * @throws ProcessException If there's an error, forking the process
     */
    private function forkProcess(callable $fn, array $params, array $sockets): int
    {
        $pid = pcntl_fork();

        if ($pid === -1) {
            // Close sockets before throwing an exception to prevent resource leaks
            $this->closeSockets($sockets);
            throw new ProcessException("Failed to fork process for asynchronous execution");
        }

        if ($pid === 0) {
            // Child process
            if (is_resource($sockets[0])) {
                fclose($sockets[0]);
            }

            $result = call_user_func($fn, ...$params);

            if (is_resource($sockets[1])) {
                fwrite($sockets[1], serialize($result));
                fclose($sockets[1]);
            }

            exit(0);
        }

        return $pid;
    }

    /**
     * Close all sockets in an array
     *
     * @param array $sockets Array of socket resources
     * @return void
     */
    private function closeSockets(array $sockets): void
    {
        foreach ($sockets as $socket) {
            if (is_resource($socket)) {
                fclose($socket);
            }
        }
    }
}
