# Contributing to AsyncAwait PHP Library

Thank you for considering contributing to the AsyncAwait PHP Library! This document provides guidelines and instructions for contributing.

## Code of Conduct

Please be respectful and considerate of others when contributing to this project.

## How Can I Contribute?

### Reporting Bugs

If you find a bug, please create an issue with the following information:

- A clear, descriptive title
- Steps to reproduce the issue
- Expected behavior
- Actual behavior
- PHP version and operating system
- Any other relevant information

### Suggesting Enhancements

If you have an idea for an enhancement, please create an issue with the following information:

- A clear, descriptive title
- A detailed description of the enhancement
- Any potential implementation details
- Why this enhancement would be useful

### Pull Requests

1. Fork the repository
2. Create a new branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run the tests (`composer test`)
5. Fix any code style issues (`composer fix-style`)
6. Commit your changes (`git commit -m 'Add some amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

## Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Run the tests: `composer test`

## Coding Standards

This project follows PSR-12 coding standards. Please ensure your code adheres to these standards.

You can check your code style with:

```bash
composer check-style
```

And automatically fix issues with:

```bash
composer fix-style
```

## Testing

All new features and bug fixes should include tests. Run the test suite with:

```bash
composer test
```

## Documentation

Please update the documentation when adding or modifying features. This includes:

- README.md for user-facing documentation
- PHPDoc comments for classes and methods
- Code comments for complex logic

## Release Process

The maintainers will handle the release process, including:

1. Updating the version number
2. Creating a new release tag
3. Publishing to Packagist

## Questions?

If you have any questions, please feel free to create an issue or contact the maintainers.

Thank you for your contributions!