<?php

namespace Testcontainers\Lifecycle;

/**
 * Registers shutdown functions and signal handlers to run a callback once on process exit.
 */
class ShutdownHandler
{
    /**
     * Whether the handler has already been registered.
     *
     * @var bool
     */
    protected static $registered = false;

    /**
     * Register a callback to be executed once on shutdown and on SIGTERM/SIGINT.
     *
     * @param callable $callback
     */
    public static function register(callable $callback)
    {
        if (self::$registered) {
            return;
        }

        register_shutdown_function(function () use ($callback) {
            $callback();
        });

        if (function_exists('pcntl_signal') && function_exists('pcntl_async_signals')) {
            pcntl_async_signals(true);
            pcntl_signal(SIGTERM, function () use ($callback) {
                $callback();
                exit(128 + SIGTERM);
            });
            pcntl_signal(SIGINT, function () use ($callback) {
                $callback();
                exit(128 + SIGINT);
            });
        }

        self::$registered = true;
    }
}
