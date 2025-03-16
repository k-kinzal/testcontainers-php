# Design Philosophy of testcontainers-php

## Architectural Patterns

### Interface-Driven Design
- Major functionalities like `Container` and `WaitStrategy` are defined as interfaces, separated from implementation details
- This ensures flexibility by allowing different implementations to be easily exchanged

### Composition Over Inheritance
- The principle of preferring composition over inheritance is adopted
- Especially in the `GenericContainer` class, multiple traits (`EnvSetting`, `PortSetting`, etc.) divided by functionality are used to combine features
- This avoids the limitations of single inheritance while improving code reusability and maintainability

### Facade Pattern
- The `Testcontainers` class functions as a facade providing a simple interface to complex container management functionality
- It hides internal complexity and provides a user-friendly API

## Design Patterns

### Strategy Pattern
- Strategy pattern is adopted for functionalities like port allocation (`PortStrategy`), startup checking (`StartupCheckStrategy`), and waiting (`WaitStrategy`)
- Different algorithms are encapsulated and can be switched at runtime
- Examples: `RandomPortStrategy`, `IsRunningStartupCheckStrategy`, `HttpWaitStrategy`, etc.

### Factory Pattern
- Factory pattern is used in classes like `DockerClientFactory`
- Object creation logic is encapsulated and separated from client code

### Provider Pattern
- Provider pattern is used in classes like `PortStrategyProvider`, `StartupCheckStrategyProvider`, and `WaitStrategyProvider`
- Centralized management of strategy registration and retrieval, enabling name-based strategy resolution

### Builder Pattern / Fluent Interface
- `GenericContainer` class provides a Fluent Interface using `withXXX()` methods
- This makes complex object construction processes readable and easy to use

### Hook Pattern
- `BeforeStartHook` and `AfterStartHook` interfaces allow custom code to be executed at specific points in the container lifecycle

## Programming Paradigms

### Declarative Programming
- Container configuration can be declaratively defined using static properties (`$IMAGE`, `$EXPOSED_PORTS`, etc.)
- This makes the code's intent clear and improves readability

### Functional Programming Elements
- Immutable object design (especially in classes within the `Types` namespace)
- Method chaining for operations with minimal side effects

## Code Structure and Modularization

### Namespace-based Logical Separation
- Functionality is separated by namespaces (`Containers`, `Docker`, `SSH`, etc.)
- Related functionality is grouped, making code organization and understanding easier

### Trait-based Feature Decomposition
- `GenericContainer` functionality is divided into multiple traits (`EnvSetting`, `PortSetting`, etc.)
- Each trait follows the single responsibility principle, handling only a specific category of settings
- This improves code maintainability and reusability

### Type-Safe Value Objects
- Value objects like `ContainerId`, `NetworkId`, and `Mount` are used to ensure type safety
- Using domain-specific types instead of primitive types reduces bugs and clarifies code intent
