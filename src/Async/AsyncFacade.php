<?php

namespace Toniette\AsyncAwait\Async;

/**
 * Facade providing a simpler API for async operations
 */
class AsyncFacade
{
    /**
     * @var AsyncManager The singleton instance of AsyncManager
     */
    private static ?AsyncManager $instance = null;

    /**
     * Get the AsyncManager instance
     *
     * @return AsyncManager
     */
    private static function getInstance(): AsyncManager
    {
        if (self::$instance === null) {
            self::$instance = new AsyncManager();
        }

        return self::$instance;
    }

    /**
     * Execute a function asynchronously in a separate process
     *
     * @param callable $fn The function to execute asynchronously
     * @param mixed ...$params Parameters to pass to the function
     * @return PromiseInterface A Promise object representing the asynchronous operation
     */
    public static function async(callable $fn, mixed ...$params): PromiseInterface
    {
        return self::getInstance()->async($fn, ...$params);
    }

    /**
     * Wait for a promise to complete and return its result
     *
     * @param PromiseInterface $promise The promise to wait for
     * @return mixed The result of the asynchronous operation
     */
    public static function await(PromiseInterface $promise): mixed
    {
        return self::getInstance()->await($promise);
    }

    /**
     * Wait for multiple promises to complete and optionally process their results
     *
     * @param callable|null $fn Optional callback function to process results
     * @param PromiseInterface ...$promises List of Promise objects to wait for
     * @return array Array of results from all promises
     */
    public static function pool(?callable $fn, PromiseInterface ...$promises): array
    {
        return self::getInstance()->pool($fn, ...$promises);
    }
}