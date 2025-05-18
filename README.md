# AsyncAwait PHP Library

A PHP library for running asynchronous operations using a Promise-based API with an async/await pattern.

## Features

- Run PHP code asynchronously in separate processes
- Promise-based API for managing asynchronous operations
- Support for awaiting individual promises or pooling multiple promises
- Object-oriented design following SOLID principles
- Backward compatibility with a functional API

## Requirements

- PHP 8.0 or higher
- PCNTL extension
- Sockets extension

## Installation

```bash
composer require toniette/async-await
```

## Usage

### Object-Oriented API

```php
use Toniette\AsyncAwait\Async\AsyncManager;

// Create an AsyncManager instance
$manager = new AsyncManager();

// Define an asynchronous function
$asyncFunction = function(string $message, int $seconds) {
    sleep($seconds);
    return "Message: $message (slept for $seconds seconds)";
};

// Create promises
$promise1 = $manager->async($asyncFunction, "First task", 1);
$promise2 = $manager->async($asyncFunction, "Second task", 2);

// Wait for results
$result1 = $manager->await($promise1);
$result2 = $manager->await($promise2);

echo "Result 1: $result1\n";
echo "Result 2: $result2\n";

// Process multiple promises in parallel
$promise3 = $manager->async($asyncFunction, "Third task", 3);
$results = $manager->pool(
    function(string ...$results) {
        echo "All tasks completed!\n";
    },
    $promise1,
    $promise2,
    $promise3
);
```

### Static Facade API

```php
use Toniette\AsyncAwait\Async\AsyncFacade;

// Create promises
$promise1 = AsyncFacade::async($asyncFunction, "Task A", 1);
$promise2 = AsyncFacade::async($asyncFunction, "Task B", 2);

// Wait for results
$result1 = AsyncFacade::await($promise1);
$result2 = AsyncFacade::await($promise2);

// Process multiple promises in parallel
$results = AsyncFacade::pool(
    function(string ...$results) {
        echo "All tasks completed!\n";
    },
    $promise1,
    $promise2
);
```

### Functional API (Backward Compatibility)

```php
// Create promises using global functions
$promise1 = async($asyncFunction, "Task A", 1);
$promise2 = async($asyncFunction, "Task B", 2);

// Wait for results
$result1 = await($promise1);
$result2 = await($promise2);

// Process multiple promises in parallel
$results = pool(
    function(string ...$results) {
        echo "All tasks completed!\n";
    },
    $promise1,
    $promise2
);
```

## Examples

See the `example.php` file for complete examples of how to use the library.

## Architecture

The library follows SOLID principles and clean code practices:

- **Single Responsibility Principle**: Each class has a single responsibility
- **Open/Closed Principle**: Classes are open for extension but closed for modification
- **Liskov Substitution Principle**: Subtypes are substitutable for their base types
- **Interface Segregation Principle**: Clients don't depend on interfaces they don't use
- **Dependency Inversion Principle**: High-level modules depend on abstractions

### Key Components

- `PromiseInterface`: Defines the contract for Promise objects
- `Promise`: Concrete implementation of a Promise
- `AsyncManager`: Manages asynchronous operations
- `AsyncFacade`: Static facade for the AsyncManager
- Global functions: Provide backward compatibility

### Exception Handling

The library uses specific exception classes for different types of errors:

- `AsyncException`: Base exception class for all library exceptions
- `SocketException`: Thrown when there's an error with socket operations
- `ProcessException`: Thrown when there's an error with process operations
- `PromiseException`: Thrown when there's an error with Promise operations

Example of handling exceptions:

```php
try {
    $promise = $manager->async($fn);
    $result = $manager->await($promise);
} catch (SocketException $e) {
    // Handle socket errors
    echo "Socket error: " . $e->getMessage();
} catch (ProcessException $e) {
    // Handle process errors
    echo "Process error: " . $e->getMessage();
} catch (PromiseException $e) {
    // Handle promise errors
    echo "Promise error: " . $e->getMessage();
} catch (AsyncException $e) {
    // Handle any other library-specific errors
    echo "Async error: " . $e->getMessage();
}
```

## Testing

The library includes a comprehensive test suite. To run the tests:

```bash
composer test
```

To check code style:

```bash
composer check-style
```

To automatically fix code style issues:

```bash
composer fix-style
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Changelog

### 1.0.0 (Initial Release)
- Implemented Promise-based API for asynchronous operations
- Added support for an async/await pattern
- Added support for pooling multiple promises
- Added backward compatibility with a functional API

## License

This library is licensed under the proprietary license. See the LICENSE file for details.

## Author

- Toniette
