# Development Practices

## Coding Conventions

Based on analysis of the project's code, the following code style is adopted:

### Class Structure
- Namespace is always written at the top of the file
- Use statements follow the namespace
- Classes and interfaces always have PHPDoc comments
- Properties have PHPDoc comments with type information

### Methods
- Methods have PHPDoc comments with purpose, arguments, and return value descriptions
- Method names are in camelCase format (e.g., `withNetworkMode`)
- Argument types are specified in PHPDoc (for PHP 5.6 compatibility)

### Variables and Properties
- Property names are in camelCase format
- Class constants are in uppercase SNAKE_CASE format or defined as static properties like $IMAGE

### Coding Conventions
- Indentation is 4 spaces
- Braces are placed on the same line, not on a new line (K&R style)
- Method chaining is allowed, and fluent interface pattern is widely used
- Braces are always used after conditional statements and control structures

## Error Handling and Robustness

### Exception Hierarchy
- Clearly defined exception hierarchy (`DockerException`, `TunnelException`, etc.)
- Specific exception classes (`NoSuchContainerException`, `PortAlreadyAllocatedException`, etc.) clearly distinguish types of errors

### Early Validation
- Early validation of input parameters to detect problems early
- Explicit exceptions for invalid inputs make debugging easier

### Automatic Resource Management
- Automatic container cleanup prevents resource leaks
- PHP destructors and shutdown handlers ensure proper resource release

## Testing Strategy

### Test-Driven Development
- The existence of extensive unit tests and E2E tests suggests a TDD approach
- Test cases also function as functional specifications

### Test Doubles
- Test cases use test doubles like mocks and stubs to isolate dependencies
- This improves test reliability and execution speed

### Test Hierarchy
- Clear separation of unit tests (`tests/Unit`) and E2E tests (`tests/E2E`)
- Different levels of tests verify code quality from multiple angles

## Compatibility and Extensibility

### Backward Compatibility Focus
- Support for a wide range of PHP versions from 5.6 to 8.3
- Minimal use of type declarations, preferring PHPDoc for type information

### Extension Points
- Clear extension points through interfaces and abstract classes
- Users can implement their own strategies and container types

### Plugin-like Architecture
- New functionality can be added without changing existing code through strategy providers
- This implements the open-closed principle

## Continuous Integration

### CI Services
- The project uses multiple CI services to ensure code quality and compatibility:
  - **CircleCI**: Used for primary CI pipeline, with badge displayed in README
  - **GitHub Actions**: Used for testing across multiple PHP versions (5.6 to 8.3), with badge displayed in README

### CI Workflow
- GitHub Actions workflow (defined in `.github/workflows/ci.yaml`):
  - Runs on every push
  - Tests against all supported PHP versions (5.6, 7.0, 7.1, 7.2, 7.3, 7.4, 8.0, 8.1, 8.2, 8.3)
  - Performs linting and testing

### README Badges
- CI status badges are displayed in the README:
  - CircleCI badge: `[![CircleCI](https://circleci.com/gh/k-kinzal/testcontainers-php.svg?style=shield)](https://circleci.com/gh/k-kinzal/testcontainers-php)`
  - GitHub Actions badge: `[![GitHub Actions](https://github.com/k-kinzal/testcontainers-php/actions/workflows/ci.yaml/badge.svg)](https://github.com/k-kinzal/testcontainers-php/actions)`

## Other Characteristic Practices

### Immutable Design Tendency
- Many methods return a new instance or return self (Fluent Interface)
- This minimizes state changes and improves predictability

### Explicit API Design
- Method names and arguments are explicit and self-explanatory (e.g., `withExposedPorts`, `withEnv`)
- This makes API usage intuitive

### Domain-Specific Language
- Fluent Interface and explicit naming provide a DSL for container configuration
- User code becomes declarative and readable

### Minimal Dependencies
- External library dependencies are kept to a minimum (mainly symfony/process and psr/log)
- This reduces the risk of compatibility issues and version conflicts

## Documentation Conventions

- Code DocComments and documentation are written in English. This follows PHP's international development practices.
- Project documentation (files in the docs/ directory, etc.) should also be written in English.
- Document titles should be concise. For example, "Getting Started" instead of "Getting Started with testcontainers-php".
- Documentation structure should separate basic usage (getting-started.md) and advanced configuration (container-configuration.md).
- Use cases (Common Use Cases) should be described in a separate file from basic usage.

## Contribution Guidelines

If you want to contribute to this project, it is recommended to follow these guidelines:

- Follow the code style described above
- Create tests using PHPUnit
- Name test classes in the format "TestedClassName + Test"
- Name test methods in the format "test + content"
- PHP-CS-Fixer can be used to automatically fix code style, as a .php-cs-fixer.dist.php file exists
