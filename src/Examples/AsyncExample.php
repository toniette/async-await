<?php

namespace Toniette\AsyncAwait\Examples;

use Random\RandomException;
use Toniette\AsyncAwait\Async\AsyncManager;
use Toniette\AsyncAwait\Async\Exception\ProcessException;
use Toniette\AsyncAwait\Async\Exception\PromiseException;
use Toniette\AsyncAwait\Async\Exception\SocketException;

/**
 * Example demonstrating the usage of the AsyncAwait library
 */
class AsyncExample
{
    /**
     * @var AsyncManager The async manager instance
     */
    private AsyncManager $asyncManager;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->asyncManager = new AsyncManager();
    }

    /**
     * Run the example
     *
     * @return void
     * @throws ProcessException
     * @throws PromiseException
     * @throws SocketException
     */
    public function run(): void
    {
        $this->demonstrateBasicAsyncAwait();
        $this->demonstratePooling();
    }

    /**
     * Demonstrate basic async/await functionality
     *
     * @return void
     * @throws ProcessException
     * @throws PromiseException
     * @throws SocketException
     */
    private function demonstrateBasicAsyncAwait(): void
    {
        echo "Running basic async/await example...\n";
        echo "Running main thread code => " . date('Y-m-d H:i:s') . "\n";

        // Create three promises for async operations
        $promise1 = $this->asyncManager->async([$this, 'exampleAsyncOperation'], 1);
        $promise2 = $this->asyncManager->async([$this, 'exampleAsyncOperation'], 2);
        $promise3 = $this->asyncManager->async([$this, 'exampleAsyncOperation'], 3);

        // Wait for each promise individually
        $result1 = $this->asyncManager->await($promise1);
        $result2 = $this->asyncManager->await($promise2);
        $result3 = $this->asyncManager->await($promise3);

        echo "Results: $result1, $result2, $result3\n";
        echo "Main thread completed => " . date('Y-m-d H:i:s') . "\n\n";
    }

    /**
     * Demonstrate pooling functionality
     *
     * @return void
     * @throws ProcessException
     * @throws SocketException
     */
    private function demonstratePooling(): void
    {
        echo "Running pooling example...\n";
        echo "Running main thread code => " . date('Y-m-d H:i:s') . "\n";

        // Create three promises for async operations
        $promise1 = $this->asyncManager->async([$this, 'exampleAsyncOperation'], 1);
        $promise2 = $this->asyncManager->async([$this, 'exampleAsyncOperation'], 2);
        $promise3 = $this->asyncManager->async([$this, 'exampleAsyncOperation'], 3);

        // Process all promises in parallel and handle results
        $this->asyncManager->pool(
            function (string ...$results): void {
                echo "All promises completed. Results:\n";
                foreach ($results as $index => $result) {
                    echo "Result " . ($index + 1) . ": $result\n";
                }
            },
            $promise1,
            $promise2,
            $promise3
        );

        echo "Main thread completed => " . date('Y-m-d H:i:s') . "\n";
    }

    /**
     * Example async function that sleeps for a random time and returns a timestamp
     *
     * @param  int $id The identifier for this async operation
     * @return string The result with a timestamp
     * @throws RandomException
     */
    public function exampleAsyncOperation(int $id): string
    {
        sleep(random_int(1, 3)); // Reduced sleep time for faster testing
        return "$id => " . date('Y-m-d H:i:s');
    }
}

// Run the example if this file is executed directly
if (basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    $example = new AsyncExample();
    try {
        $example->run();
    } catch (ProcessException|PromiseException|SocketException $e) {
        echo $e->getMessage();
    }
}