<?php

namespace Testcontainers\Containers\GenericContainer;

use Testcontainers\Containers\PortStrategy\AlreadyExistsPortStrategyException;
use Testcontainers\Containers\PortStrategy\PortStrategy;
use Testcontainers\Containers\PortStrategy\PortStrategyProvider;
use Testcontainers\Containers\PortStrategy\RandomPortStrategy;

/**
 * ExposedPortSetting is a trait that provides the ability to expose ports on a container.
 *
 * Two formats are supported:
 * 1. static variable `$EXPOSED_PORTS` and `PORT_STRATEGY`:
 *
 * <code>
 * class YourContainer extends GenericContainer
 * {
 *     protected static $EXPOSED_PORTS = [80, 443];
 *     protected static $PORT_STRATEGY = 'local_random';
 * }
 * </code>
 *
 * 2. method `withExposedPorts`:
 *
 * <code>
 * $container = (new YourContainer('image'))
 *     ->withExposedPorts(80)
 *     ->withPortStrategy(new LocalRandomPortStrategy());
 * </code>
 */
trait PortSetting
{
    /**
     * Define the default ports to be exposed by the container.
     *
     * @var null|int[]
     * @psalm-var mixed
     */
    protected static $EXPOSED_PORTS;

    /**
     * Define the default ports to be exposed by the container. Alias for `EXPOSED_PORTS`.
     *
     * @var null|int[]
     * @psalm-var mixed
     */
    protected static $EXPOSE;

    /**
     * Define the default ports to be exposed by the container. Alias for `EXPOSED_PORTS`.
     *
     * @var null|int[]
     * @psalm-var mixed
     */
    protected static $PORTS;

    /**
     * Define the default port strategy to be used for the container.
     *
     * @var null|string
     * @psalm-var mixed
     */
    protected static $PORT_STRATEGY;

    /**
     * The ports to be exposed by the container.
     *
     * @var int[]
     */
    private $exposedPorts = [];

    /**
     * The port strategy to be used for the container.
     *
     * @var null|PortStrategy
     */
    private $portStrategy;

    /**
     * The port strategy provider.
     *
     * @var PortStrategyProvider
     */
    private $portStrategyProvider;

    /**
     * Set the port that this container listens on.
     *
     * @param int|string $port the port to expose
     * @psalm-param mixed $port
     *
     * @return self
     */
    public function withExposedPort($port)
    {
        self::ensureIntOrDigitString($port);

        if ($port < 1 || $port > 65535) {
            throw new \InvalidArgumentException('Port must be between 1 and 65535');
        }
        $port = (int) $port;

        $this->exposedPorts[] = $port;

        return $this;
    }

    /**
     * Set the port that this container listens on. Alias for `withExposedPort`.
     *
     * @param int|string $port the port to expose
     * @psalm-param mixed $port
     *
     * @return self
     */
    public function withExpose($port)
    {
        return $this->withExposedPort($port);
    }

    /**
     * Set the port that this container listens on. Alias for `withExposedPort`.
     *
     * @param int|string $port the port to expose
     * @psalm-param mixed $port
     *
     * @return self
     */
    public function withPort($port)
    {
        return $this->withExposedPort($port);
    }

    /**
     * Set the ports that this container listens on.
     *
     * @param int[] $ports the ports to expose
     * @psalm-param mixed $ports
     *
     * @return self
     */
    public function withExposedPorts($ports)
    {
        self::ensureArray($ports);

        $normalizedPorts = [];
        foreach (array_keys($ports) as $key) {
            self::ensureIntOrDigitString($ports[$key]);

            if ($ports[$key] < 1 || $ports[$key] > 65535) {
                throw new \InvalidArgumentException('Port must be between 1 and 65535');
            }
            $normalizedPorts[] = (int) $ports[$key];
        }
        $ports = $normalizedPorts;

        $this->exposedPorts = $ports;

        return $this;
    }

    /**
     * Set the ports that this container listens on. Alias for `withExposedPorts`.
     *
     * @param int[] $ports the ports to expose
     * @psalm-param mixed $ports
     *
     * @return self
     */
    public function withExposes($ports)
    {
        return $this->withExposedPorts($ports);
    }

    /**
     * Set the ports that this container listens on. Alias for `withExposedPorts`.
     *
     * @param int[] $ports the ports to expose
     * @psalm-param mixed $ports
     *
     * @return self
     */
    public function withPorts($ports)
    {
        return $this->withExposedPorts($ports);
    }

    /**
     * Set the port strategy used for determining the ports that the container listens on.
     *
     * @param PortStrategy $strategy the port strategy to use
     * @psalm-param mixed $strategy
     *
     * @return self
     */
    public function withPortStrategy($strategy)
    {
        self::ensureInstanceOf($strategy, PortStrategy::class);

        $this->portStrategy = $strategy;

        return $this;
    }

    /**
     * Retrieve the ports to be exposed by the container.
     *
     * This method checks for ports defined in the following order:
     * 1. Static variable `$EXPOSED_PORTS`
     * 2. Static variable `$EXPOSE`
     * 3. Static variable `$PORTS`
     * 4. Instance variable `$exposedPorts`
     *
     * @return int[] the list of ports to be exposed
     */
    protected function exposedPorts()
    {
        if (static::$EXPOSED_PORTS !== null) {
            $ports = self::ensureArray(static::$EXPOSED_PORTS);

        } elseif (static::$EXPOSE !== null) {
            $ports = self::ensureArray(static::$EXPOSE);

        } elseif (static::$PORTS !== null) {
            $ports = self::ensureArray(static::$PORTS);

        } else {
            $ports = $this->exposedPorts;
        }
        $normalizedPorts = [];
        foreach (array_keys($ports) as $key) {
            self::ensureIntOrDigitString($ports[$key]);

            if ($ports[$key] < 1 || $ports[$key] > 65535) {
                throw new \InvalidArgumentException('Port must be between 1 and 65535');
            }
            $normalizedPorts[] = (int) $ports[$key];
        }

        return $normalizedPorts;
    }

    /**
     * Retrieve the port strategy for the container.
     *
     * This method returns the port strategy that should be used for the container.
     * If a specific port strategy is set, it will return that. Otherwise, it will
     * attempt to retrieve the default port strategy from the provider.
     *
     * @return null|PortStrategy the port strategy to be used, or null if none is set
     */
    protected function portStrategy()
    {
        if ($this->portStrategyProvider === null) {
            $this->portStrategyProvider = new PortStrategyProvider();
            $this->registerPortStrategy($this->portStrategyProvider);
        }
        if (static::$PORT_STRATEGY !== null) {
            $name = self::ensureString(static::$PORT_STRATEGY);

            $strategy = $this->portStrategyProvider->get($name);
            if (!$strategy) {
                throw new \LogicException('Port strategy not found: '.$name);
            }

            return $strategy;
        }
        if ($this->portStrategy) {
            return $this->portStrategy;
        }

        $containerPorts = $this->exposedPorts();
        if (count($containerPorts) > 0) {
            return $this->portStrategyProvider->get('random');
        }

        return null;
    }

    /**
     * Retrieve Map of ports to be exposed by the container.
     *
     * @return array<int, int> key-value pairs of container ports to host ports
     */
    protected function ports()
    {
        $containerPorts = $this->exposedPorts();
        $strategy = $this->portStrategy();
        if ($strategy) {
            $ports = [];
            foreach ($containerPorts as $containerPort) {
                $hostPort = $strategy->getPort();
                $ports[$containerPort] = $hostPort;
            }

            return $ports;
        }

        return [];
    }

    /**
     * Register a port strategy.
     *
     * @param PortStrategyProvider $provider the port strategy provider
     */
    protected function registerPortStrategy($provider)
    {
        try {
            $provider->register('random', new RandomPortStrategy());
        } catch (AlreadyExistsPortStrategyException $e) {
            throw new \LogicException('Port strategy already registered: random', 0, $e);
        }
    }
}
