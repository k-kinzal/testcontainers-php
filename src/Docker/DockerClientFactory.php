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
     * @var array<string, DockerClient>
     */
    private static $client = [];

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
     * @param array $config The configuration options for the Docker client.
     * @return DockerClient A new instance of DockerClient.
     */
    public static function create($config = [])
    {
        $config = array_merge(self::$config, $config);
        $hash = md5(serialize($config));

        if (!isset(self::$client[$hash])) {
            $client = new DockerClient();
            if (isset($config['command'])) {
                $client = $client->withCommand($config['command']);
            }
            if (isset($config['globalOptions'])) {
                $client = $client->withGlobalOptions($config['globalOptions']);
            }
            if (isset($config['cwd'])) {
                $client = $client->withCwd($config['cwd']);
            }
            if (isset($config['env'])) {
                $client = $client->withEnv($config['env']);
            }
            if (isset($config['input'])) {
                $client = $client->withInput($config['input']);
            }
            if (isset($config['timeout'])) {
                $client = $client->withTimeout($config['timeout']);
            }
            if (isset($config['procOptions'])) {
                $client = $client->withProcOptions($config['procOptions']);
            }
            self::$client[$hash] = $client;
        }

        return clone self::$client[$hash];
    }
}
