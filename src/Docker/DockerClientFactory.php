<?php

namespace Testcontainers\Docker;

/**
 * A factory for creating DockerClient instances.
 *
 * This class provides a static method for creating new DockerClient instances.
 * It ensures that only one instance of DockerClient is created and shared across
 * all calls to the `create` method.
 */
class DockerClientFactory
{
    /**
     * The Docker client instance.
     *
     * This static property holds the Docker client instance. It is initialized
     * when the `create` method is called for the first time.
     *
     * @var DockerClient|null
     */
    private static $client;

    /**
     * The configuration options for the Docker client.
     *
     * This static property holds the configuration options for the Docker client.
     * It is set using the `config` method before the `create` method is called.
     *
     * @var array
     */
    private static $config = [];

    /**
     * Set the configuration options for the Docker client.
     *
     * This method sets the configuration options for the Docker client. The configuration
     * options are used to initialize the Docker client instance when the `create` method
     * is called.
     *
     * @param array $config The configuration options for the Docker client.
     */
    public static function config($config = [])
    {
        self::$config = $config;
    }

    /**
     * Create and return a new DockerClient instance.
     *
     * This method initializes the Docker client instance if it has not been created yet.
     * It returns a clone of the Docker client to ensure that each call to this method
     * provides a fresh instance.
     *
     * @return DockerClient A new instance of DockerClient.
     */
    public static function create()
    {
        if (self::$client === null) {
            $client = new DockerClient();
            if (isset(self::$config['command'])) {
                $client = $client->withCommand(self::$config['command']);
            }
            if (isset(self::$config['globalOptions'])) {
                $client = $client->withGlobalOptions(self::$config['globalOptions']);
            }
            if (isset(self::$config['cwd'])) {
                $client = $client->withCwd(self::$config['cwd']);
            }
            if (isset(self::$config['env'])) {
                $client = $client->withEnv(self::$config['env']);
            }
            if (isset(self::$config['input'])) {
                $client = $client->withInput(self::$config['input']);
            }
            if (isset(self::$config['timeout'])) {
                $client = $client->withTimeout(self::$config['timeout']);
            }
            if (isset(self::$config['procOptions'])) {
                $client = $client->withProcOptions(self::$config['procOptions']);
            }
            self::$client = $client;
        }

        return clone self::$client;
    }
}
