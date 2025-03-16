# testcontainers-php Testing Approach

This document provides a detailed analysis of the testing approach, methodology, and philosophy in the testcontainers-php project.

## 1. Test Structure and Organization

### 1.1 Test Directory Structure

The project's tests are organized in a clearly separated structure:

- **tests/Unit/**: Unit tests (isolated tests of individual components)
- **tests/E2E/**: End-to-end tests (integration tests in real environments)
- **tests/Images/**: Container image definitions for testing

This structure clearly distinguishes the types and purposes of tests, making test execution and management easier.

### 1.2 PHPUnit Configuration

The `phpunit.xml.dist` file contains important settings:

- Clear separation of test suites (unit and e2e)
- Random execution order (`executionOrder="random"`) to detect dependencies between tests
- Stop on failure (`stopOnFailure="true"`) for early problem detection
- Maximum error reporting (`error_reporting="-1"`)

## 2. Test Design Patterns

### 2.1 Test Class Inheritance Pattern

The project extensively uses abstract test cases to reuse common test logic:

- **WaitStrategyTestCase**: Base class for wait strategy tests
- **PortStrategyTestCase**: Base class for port strategy tests

These abstract classes define common test requirements for specific interfaces, and concrete implementation classes extend them to test specific implementation details.

```php
// WaitStrategyTestCase
abstract class WaitStrategyTestCase extends TestCase
{
    abstract public function resolveWaitStrategy();
}

// HostPortWaitStrategyTest
class HostPortWaitStrategyTest extends WaitStrategyTestCase
{
    public function resolveWaitStrategy()
    {
        return new HostPortWaitStrategy();
    }
    // ...
}
```

This pattern is unconditionally applied to all components that use the strategy pattern (PortStrategy, WaitStrategy, StartupCheckStrategy, etc.) to ensure consistent testing of interfaces.

### 2.2 Single Responsibility Principle Applied to Tests

Each test class focuses on testing a specific component, and each test method tests a specific functionality or behavior:

```php
// DockerClientTest focuses only on DockerClient functionality
class DockerClientTest extends TestCase
{
    public function testClone()
    {
        $client = new DockerClient();
        $cloned = clone $client;

        $this->assertNotSame($client, $cloned);
        $this->assertEquals($client, $cloned);
    }
}
```

This pattern is unconditionally applied to all test classes to clarify the purpose and scope of tests, improve maintainability and readability, and make problem identification easier when tests fail.

### 2.3 Explicit Setup and Assertion

Tests follow the "Arrange-Act-Assert" or "Given-When-Then" pattern:

```php
// HostPortWaitStrategyTest example
public function testWaitUntilReady()
{
    // Arrange (Given)
    $instance = new GenericContainerInstance([
        'containerId' => new ContainerId('8188d93d8a27'),
        'ports' => [80 => 8239],
    ]);
    $probe = $this->createMock(PortProbe::class);
    $probe->method('available')
        ->willReturnOnConsecutiveCalls(false, false, true);
    $strategy = new HostPortWaitStrategy($probe);
    
    // Act (When)
    $strategy->waitUntilReady($instance);

    // Assert (Then)
    $this->assertTrue(true);
}
```

This pattern is unconditionally applied to all test methods to clarify the intent and flow of tests, improve readability and understandability, and make debugging easier when tests fail.

## 3. Test Techniques

### 3.1 Test-Driven Development

The project follows a test-driven development (TDD) approach, as evidenced by the extensive unit tests and E2E tests. Test cases also function as functional specifications, documenting the expected behavior of the code.

This approach aligns with the project's focus on behavior-focused testing and design for changeability, as tests are written before the implementation, ensuring that the implementation meets the specified behavior.

### 3.2 Test Doubles Usage

The project uses test doubles (mocks, stubs) under specific conditions:

1. When the dependent component is injectable and the tested class doesn't care about the implementation details of the dependent component
2. When using the actual dependent component would make tests non-deterministic or slow
3. When creating appropriate fakes is difficult

```php
// HostPortWaitStrategyTest example
$probe = $this->createMock(PortProbe::class);
$probe->method('available')
    ->willReturnOnConsecutiveCalls(false, false, true);
```

In the `HostPortWaitStrategy` implementation, the strategy depends on the `PortProbe` interface but doesn't care about its specific implementation:

```php
// HostPortWaitStrategy implementation
public function __construct($probe = null)
{
    $this->probe = $probe ?: new PortProbeTcp();
}

// waitUntilReady method only uses the probe's interface
public function waitUntilReady($instance)
{
    // ...
    if ($this->probe->available($host, $port)) {
        break;
    }
    // ...
}
```

The reasons for using test doubles in this case:
1. `HostPortWaitStrategy` only depends on the `PortProbe` interface, not its implementation details
2. `PortProbe` is injectable (constructor injection)
3. Using the actual `PortProbeTcp` would make tests non-deterministic (depends on network port state)
4. Creating an appropriate fake would be difficult (would need to simulate network port state)

### 3.2 Exception Testing

The project uses exception testing for methods that throw exceptions:

```php
// HostPortWaitStrategyTest example
public function testWaitUntilReadyThrowsWaitingTimeoutException()
{
    $this->expectException(WaitingTimeoutException::class);

    $instance = new GenericContainerInstance([
        'containerId' => new ContainerId('8188d93d8a27'),
        'ports' => [80 => 8239],
    ]);
    $strategy = (new HostPortWaitStrategy())->withTimeoutSeconds(0);

    $strategy->waitUntilReady($instance);
}
```

This technique is unconditionally applied to all methods that throw exceptions to verify the correctness of error handling code, ensure exception conditions are properly handled, and clarify the specifications for error cases.

### 3.3 Conditional Test Execution

The project uses conditional test execution for tests that only make sense in specific environments:

```php
// DockerExecutorTest example
if (getenv('CIRCLECI') !== 'true' || getenv('DOCKER_HOST') === false) {
    $this->markTestSkipped('This test is only for CircleCI (docker executor)');
}
```

This technique is applied conditionally when:
1. Tests only make sense in specific environments (like CircleCI)
2. Tests require specific configurations (like Docker Host)
3. Tests can only run when specific resources (like ports) are available

## 4. Test Types and Scope

### 4.1 Unit Tests

The project includes unit tests for isolated testing of individual components:

- **Setting class tests**: `PortSettingTest`, `EnvSettingTest`, etc.
- **Strategy class tests**: `RandomPortStrategyTest`, `HttpWaitStrategyTest`, etc.
- **Type class tests**: `MountTest`, `NetworkModeTest`, etc.

### 4.2 Integration Tests

The project includes integration tests for testing the interaction of multiple components:

- **GenericContainerTest**: Container creation and startup
- **GenericContainerInstanceTest**: Interaction with running containers

### 4.3 End-to-End Tests

The project includes end-to-end tests for testing complete functionality in real environments:

- **DockerExecutorTest**: Testing in CircleCI's Docker executor environment
- **MachineExecutorTest**: Testing in CircleCI's machine executor environment

## 5. Test Resource Management

### 5.1 Test Resource Management Pattern

The project uses the test resource management pattern for tests that create external resources (like Docker containers):

```php
// TestcontainersTest example
try {
    $instance = Testcontainers::run(AlpineContainer::class);
    // Test code
} finally {
    Testcontainers::stop();
}
```

This pattern is applied conditionally when:
1. Tests create external resources (like Docker containers)
2. Resources need to be cleaned up at the end of the test
3. Resources are not automatically cleaned up

### 5.2 Environment Detection

The project uses environment detection for tests that depend on the environment:

```php
// GenericContainerInstanceTest example
public function testGetHostFromOverride()
{
    try {
        putenv('TESTCONTAINERS_HOST_OVERRIDE=override.local');
        $instance = new GenericContainerInstance([
            'containerId' => new ContainerId('8188d93d8a27'),
        ]);

        $this->assertSame('override.local', $instance->getHost());
    } finally {
        putenv('TESTCONTAINERS_HOST_OVERRIDE');
    }
}
```

This technique is applied conditionally when:
1. Tests run in different environments (local, CI, inside Docker, etc.)
2. Tests require environment-specific settings
3. Tests depend on environment variables

## 6. Test Cases

### 6.1 Basic Functionality Tests

The project includes tests for basic functionality:

```php
// RunCommandTest example
public function testRun()
{
    $client = new DockerClient();
    $output = $client->run('alpine:latest', 'echo', ['Hello, World!']);

    $this->assertInstanceOf(DockerRunOutput::class, $output);
    $this->assertSame(0, $output->getExitCode());
    $this->assertSame("Hello, World!\n", $output->getOutput());
}
```

### 6.2 Error Case Tests

The project includes tests for error cases:

```php
// RunCommandTest example
public function testRunWithPortConflict()
{
    $this->expectException(PortAlreadyAllocatedException::class);

    $instance = Testcontainers::run(DinD::class);

    $client = new DockerClient();
    $client->withGlobalOptions([
        'host' => 'tcp://' . $instance->getHost() . ':' . $instance->getMappedPort(2375),
    ]);
    $client->run('alpine:latest', null, ['tail', '-f', '/dev/null'], [
        'detach' => true,
        'publish' => ['38793:80'],
    ]);
    $client->run('alpine:latest', null, ['tail', '-f', '/dev/null'], [
        'detach' => true,
        'publish' => ['38793:80'],
    ]);
}
```

This is not a test pattern but a specific test case for the port conflict scenario, verifying that the appropriate exception is thrown when a port conflict occurs.

### 6.3 Environment-Dependent Tests

The project includes tests that depend on the environment:

```php
// GenericContainerInstanceTest example
public function testGetHostFromDockerHost()
{
    $dind = Testcontainers::run(DinD::class);
    $client = DockerClientFactory::create([
        'globalOptions' => [
            'host' => 'tcp://' . $dind->getHost() . ':' . $dind->getMappedPort(2375)
        ],
    ]);

    $instance = new GenericContainerInstance([
        'containerId' => new ContainerId('8188d93d8a27'),
    ]);
    $instance->setDockerClient($client);

    $this->assertSame($dind->getHost(), $instance->getHost());
}
```

## 7. Test Environment and Configuration

### 7.1 Test Container Images

The project defines special container classes for testing:

- **AlpineContainer**: Lightweight container for basic testing
- **DinD**: Docker-in-Docker container for testing

These classes provide specific configurations and behaviors needed for testing.

### 7.2 CI/CD Integration

The tests are integrated with CI/CD environments (CircleCI, GitHub Actions) and run on multiple PHP versions (5.6 to 8.3).

## 8. Testing Principles

### 8.1 Interface-Oriented Testing

Tests are performed against interfaces rather than implementations:

```php
// PortStrategyTestCase example
public function testInterfaceGetPort()
{
    $strategy = $this->resolvePortStrategy();
    $port = $strategy->getPort();

    $this->assertTrue(is_int($port));
    $this->assertGreaterThanOrEqual(49152, $port);
    $this->assertLessThanOrEqual(65535, $port);
}
```

### 8.2 Composition-Oriented Testing

Tested classes have injectable dependencies:

```php
// HostPortWaitStrategy example
public function __construct($probe = null)
{
    $this->probe = $probe ?: new PortProbeTcp();
}
```

### 8.3 Isolated Testing

Each test is independent of other tests:

```php
// Each test creates its own container instance
$container = new GenericContainer('alpine:latest');
$instance = $container->start();
```

## 9. Design for Changeability and Refactorability

### 9.1 Emphasis on Changeability

The project places a strong emphasis on designing for changeability (also known as "modifiability" in software quality attributes). This is evident in several aspects of the codebase:

1. **Interface Segregation Principle (ISP)**: The project uses small, focused interfaces (like `PortStrategy`, `WaitStrategy`) that are easier to implement and modify.
2. **Dependency Inversion Principle (DIP)**: High-level modules depend on abstractions, not concrete implementations, making it easier to change implementations without affecting dependent code.
3. **Open/Closed Principle (OCP)**: The strategy pattern implementations are open for extension but closed for modification, allowing new strategies to be added without changing existing code.

These SOLID principles collectively contribute to a codebase that can accommodate change with minimal impact on existing functionality.

### 9.2 Support for Refactorability

Refactorability (the ease with which code can be restructured without changing its external behavior) is supported through:

1. **Cohesive Components**: Each class and trait has a single, well-defined responsibility, making it easier to refactor individual components without affecting others.
2. **Low Coupling**: Dependencies are minimized and abstracted through interfaces, reducing the ripple effect of changes.
3. **Encapsulation**: Implementation details are hidden behind well-defined interfaces, allowing internal changes without affecting client code.

### 9.3 Behavior-Focused Testing

The project's tests focus on verifying behavior rather than implementation details, which is crucial for supporting refactorability:

1. **Black-Box Testing Approach**: Tests verify the expected outputs for given inputs without knowledge of internal implementation.
2. **Contract Testing**: Tests verify that components adhere to their interface contracts, not specific implementation details.
3. **State Verification Over Interaction Verification**: Where possible, tests verify the final state or result rather than the specific interactions between components.

For example, in the `PortStrategyTestCase`, the tests verify that the strategy returns a valid port within the expected range, not how it selects that port:

```php
public function testInterfaceGetPort()
{
    $strategy = $this->resolvePortStrategy();
    $port = $strategy->getPort();

    $this->assertTrue(is_int($port));
    $this->assertGreaterThanOrEqual(49152, $port);
    $this->assertLessThanOrEqual(65535, $port);
}
```

### 9.4 Rationale for Behavior-Focused Testing

The project adopts behavior-focused testing for several important reasons:

1. **Resilience to Refactoring**: When tests focus on behavior rather than implementation, the implementation can be refactored without breaking tests, as long as the behavior remains the same. This is known as "refactoring-friendly testing" in the technical literature.

2. **Reduced Test Maintenance**: Implementation-focused tests often require updates when implementation details change, even if the behavior doesn't change. Behavior-focused tests reduce this maintenance burden, aligning with the concept of "sustainable test suites" described by testing experts.

3. **Better Documentation of Intent**: Behavior-focused tests serve as executable specifications that document what the code should do, not how it does it. This aligns with the "tests as documentation" principle in Behavior-Driven Development (BDD).

4. **Support for Evolutionary Design**: By focusing on behavior, tests allow the design to evolve incrementally without constant test rewrites. This supports the "emergent design" principle from Extreme Programming (XP).

5. **Improved Test Stability**: Tests that depend on implementation details are more brittle and prone to false negatives. Behavior-focused tests are more stable, providing what Michael Feathers calls "tests that withstand change."

## 10. Practical Application of Testing Principles

The project's testing approach combines various patterns, techniques, and cases to form a comprehensive testing strategy. The basic patterns (test class inheritance, single responsibility, explicit setup and assertion) are applied unconditionally to all tests, while specific techniques (test doubles, conditional execution, resource management) are applied conditionally based on the needs of each test.

This balanced approach ensures that tests are both comprehensive and efficient, providing high confidence in the code's correctness while keeping test execution time reasonable. Moreover, the focus on behavior testing and design for changeability creates a codebase that can evolve over time without accumulating technical debt, embodying what Robert C. Martin calls "clean code" and Kent Beck describes as "simple design."

## 11. Testing Pitfalls and Considerations

### 11.1 Misalignment Between Test Purpose and Implementation

A common pitfall in testing is when the test implementation doesn't align with its intended purpose:

```php
// Example of misaligned test purpose and implementation
public function testShutdownHandlerRegistration()
{
    $instance1 = Testcontainers::run(new AlpineContainer());
    $this->assertTrue($instance1->isRunning());
    
    $instance2 = Testcontainers::run(new AlpineContainer());
    $this->assertTrue($instance2->isRunning());
    
    Testcontainers::stop();
    
    $this->assertFalse($instance1->isRunning());
    $this->assertFalse($instance2->isRunning());
}
```

In this example, the test name suggests it's testing shutdown handler registration, but it's actually testing the `Testcontainers::stop()` method's ability to stop multiple containers. The test name should reflect its actual purpose, such as `testStopMultipleInstances()`.

**Lessons Learned:**
- Test names should accurately reflect what the test is actually verifying
- When reviewing tests, ensure the implementation matches the stated purpose
- Refactor test names when the implementation changes to maintain clarity

### 11.2 Inconsistency Between Documentation and Implementation

Another pitfall is when the implementation doesn't match the documented behavior:

```php
/**
 * @param array{
 *     containerId: ContainerId,
 *     labels?: array<string, string>[]|null,
 *     ports?: array<int, int>,
 *     pull?: ImagePullPolicy|null,
 *     privileged?: bool,
 * } $containerDef The container definition.
 */
public function __construct($containerDef = [])
{
    $this->containerDef = $containerDef;
}

// Implementation that doesn't match documentation
public function getLabels()
{
    if (!isset($this->containerDef['labels'])) {
        return [];
    }
    if (!is_array($this->containerDef['labels'])) { // This check isn't documented
        return [];
    }
    return $this->containerDef['labels'] ?: [];
}
```

In this example, the `getLabels()` method includes an `is_array()` check that isn't documented in the constructor's PHPDoc. This creates a discrepancy between the documented behavior and the actual implementation.

**Lessons Learned:**
- Ensure implementation matches documentation, especially type hints and PHPDoc
- When writing tests, refer to the documentation to understand expected behavior
- Update documentation when implementation changes, or vice versa

### 11.3 Duplicate Tests

Duplicate tests can lead to maintenance issues and slower test execution:

```php
// Example of duplicate tests
public function testStopMultipleInstances()
{
    $instance1 = Testcontainers::run(new AlpineContainer());
    $instance2 = Testcontainers::run(new AlpineContainer());
    $instance3 = Testcontainers::run(new AlpineContainer());
    
    $this->assertTrue($instance1->isRunning());
    $this->assertTrue($instance2->isRunning());
    $this->assertTrue($instance3->isRunning());
    
    Testcontainers::stop();
    
    $this->assertFalse($instance1->isRunning());
    $this->assertFalse($instance2->isRunning());
    $this->assertFalse($instance3->isRunning());
}

public function testStopAllContainers()
{
    $instance1 = Testcontainers::run(new AlpineContainer());
    $this->assertTrue($instance1->isRunning());
    
    $instance2 = Testcontainers::run(new AlpineContainer());
    $this->assertTrue($instance2->isRunning());
    
    Testcontainers::stop();
    
    $this->assertFalse($instance1->isRunning());
    $this->assertFalse($instance2->isRunning());
}
```

These tests are essentially testing the same functionality with minor variations.

**Lessons Learned:**
- Before adding new tests, check for existing tests that verify the same behavior
- Consolidate duplicate tests into a single, comprehensive test
- Use parameterized tests for testing variations of the same behavior

### 11.4 Testing Untestable Features

Some features are inherently difficult or impossible to test in a unit test context:

```php
// Example of attempting to test an untestable feature
public function testShutdownHandlerRegistration()
{
    // This test attempts to verify that shutdown handlers are registered,
    // but shutdown handlers only execute when the PHP process terminates,
    // which can't be simulated in a unit test
}
```

Shutdown handlers, signal handlers, and other process-level features are difficult to test in unit tests because they depend on process termination or signal delivery, which can't be easily simulated in a test environment.

**Lessons Learned:**
- Identify untestable features early in the design process
- Design for testability by isolating untestable features behind interfaces
- Use integration or end-to-end tests for features that can't be unit tested
- Consider alternative designs that achieve the same goal in a more testable way

### 11.5 Appropriate Use of Test Doubles

Using test doubles (mocks, stubs) inappropriately can lead to brittle tests:

```php
// Example of inappropriate use of test doubles
public function testGetHost()
{
    $mockClient = $this->createMock(DockerClient::class);
    $mockClient->method('getHost')
        ->willReturn('http://example.com:2375');

    $instance = new GenericContainerInstance([
        'containerId' => new ContainerId('8188d93d8a27'),
    ]);
    $instance->setDockerClient($mockClient);

    $this->assertSame('example.com', $instance->getHost());
}
```

In this example, using a mock for `DockerClient` is appropriate because:
1. The actual implementation would depend on the environment
2. We're testing the host extraction logic, not the `DockerClient` implementation
3. The `DockerClient` is injectable and the test only cares about its interface

However, mocking too much or mocking implementation details can lead to tests that are tightly coupled to the implementation, making refactoring difficult.

**Lessons Learned:**
- Use test doubles judiciously, only when necessary
- Mock interfaces rather than concrete classes when possible
- Focus on behavior verification rather than implementation verification
- Consider the trade-offs between using real objects and test doubles
