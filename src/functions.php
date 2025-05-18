<?php

use Toniette\AsyncAwait\Async\AsyncFacade;
use Toniette\AsyncAwait\Async\Exception\ProcessException;
use Toniette\AsyncAwait\Async\Exception\PromiseException;
use Toniette\AsyncAwait\Async\Exception\SocketException;
use Toniette\AsyncAwait\Async\PromiseInterface;

/**
 * Execute a function asynchronously in a separate process
 *
 * @param  callable $fn        The function to execute asynchronously
 * @param  mixed    ...$params Parameters to pass to the function
 * @return PromiseInterface A Promise object representing the asynchronous operation
 * @throws ProcessException
 * @throws SocketException
 */
function async(callable $fn, mixed ...$params): PromiseInterface
{
    return AsyncFacade::async($fn, ...$params);
}

/**
 * Wait for a promise to complete and return its result
 *
 * @param  PromiseInterface $promise The promise to wait for
 * @return mixed The result of the asynchronous operation
 * @throws PromiseException
 */
function await(PromiseInterface $promise): mixed
{
    return AsyncFacade::await($promise);
}

/**
 * Wait for multiple promises to complete and optionally process their results
 *
 * @param  callable|null    $fn          Optional callback function to process results
 * @param  PromiseInterface ...$promises List of Promise objects to wait for
 * @return array Array of results from all promises
 */
function pool(?callable $fn, PromiseInterface ...$promises): array
{
    return AsyncFacade::pool($fn, ...$promises);
}