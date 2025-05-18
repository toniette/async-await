<?php

require_once __DIR__ . '/vendor/autoload.php';

use Toniette\AsyncAwait\Async\AsyncManager;
use Toniette\AsyncAwait\Examples\AsyncExample;

echo "AsyncAwait Library Examples\n";
echo "==========================\n\n";

echo "1. Using the AsyncExample class:\n";
echo "-------------------------------\n";
$example = new AsyncExample();
$example->run();

echo "\n2. Using the AsyncManager directly:\n";
echo "---------------------------------\n";
$manager = new AsyncManager();

// Define a simple async function
$asyncFunction = function(string $message, int $seconds) {
    sleep($seconds);
    return "Message: $message (slept for $seconds seconds)";
};

// Create promises
$promise1 = $manager->async($asyncFunction, "First task", 1);
$promise2 = $manager->async($asyncFunction, "Second task", 2);

// Wait for results
echo "Waiting for results...\n";
$result1 = $manager->await($promise1);
$result2 = $manager->await($promise2);

echo "Result 1: $result1\n";
echo "Result 2: $result2\n";

echo "\n3. Using the global functions (backward compatibility):\n";
echo "----------------------------------------------------\n";

// Create promises using global functions
$promise1 = async($asyncFunction, "Task A", 1);
$promise2 = async($asyncFunction, "Task B", 2);
$promise3 = async($asyncFunction, "Task C", 3);

// Use pool to wait for all promises
echo "Using pool to wait for all promises...\n";
$results = pool(function(string ...$results) {
    echo "All tasks completed!\n";
}, $promise1, $promise2, $promise3);

echo "Results from pool:\n";
foreach ($results as $index => $result) {
    echo "Result " . ($index + 1) . ": $result\n";
}

echo "\nExamples completed successfully!\n";