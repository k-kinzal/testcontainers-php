<?php

namespace Testcontainers\Containers\GenericContainer;

use Testcontainers\Environments;

/**
 * SSHPortForwardSetting is a trait that provides the ability to set up SSH port forwarding to the remote host that starts the container.
 *
 * Two formats are supported:
 * 1. static variable `$SSH_PORT_FORWARD`:
 *
 * <code>
 *     class YourContainer extends GenericContainer
 *     {
 *         protected static $EXPOSED_PORTS = [80];
 *
 *         protected static $SSH_PORT_FORWARD = 'user@host:port';
 *     }
 * </code>
 *
 * 2. method `withSSHPortForward`:
 *
 * <code>
 *     $container = (new YourContainer('image'))
 *        ->withExposedPort(80)
 *        ->withSSHPortForward('host', 22, 'user');
 * </code>
 */
trait SSHPortForwardSetting
{
    /**
     * Define the default SSH port forwarding to be used for the container.
     *
     * @var null|bool|string
     */
    protected static $SSH_PORT_FORWARD;

    /**
     * @var array{
     *             sshUser?: string|null,
     *             sshHost: string,
     *             sshPort?: int|null,
     *             }|bool|null
     */
    private $sshPortForward;

    /**
     * Set up SSH port forwarding to the remote host that starts the container.
     *
     * @param string      $sshHost the SSH host to forward to
     * @param null|int    $sshPort the SSH port to forward to
     * @param null|string $sshUser the SSH user to use for the connection
     *
     * @return $this
     */
    public function withSSHPortForward($sshHost, $sshPort = null, $sshUser = null)
    {
        $this->sshPortForward = [
            'sshUser' => $sshUser,
            'sshHost' => $sshHost,
            'sshPort' => $sshPort,
        ];

        return $this;
    }

    /**
     * Retrieve the SSH port forwarding to be used for the container.
     *
     * @return array{
     *                sshUser?: string|null,
     *                sshHost?: string|null,
     *                sshPort?: int|null,
     *                }|null
     */
    protected function sshPortForward()
    {
        if (null !== self::$SSH_PORT_FORWARD) {
            if (is_bool(self::$SSH_PORT_FORWARD)) {
                return self::$SSH_PORT_FORWARD ? [
                    'sshUser' => null,
                    'sshHost' => null,
                    'sshPort' => null,
                ] : null;
            }

            return $this->parseSSHString(self::$SSH_PORT_FORWARD);
        }
        if (null !== $this->sshPortForward) {
            if (is_bool($this->sshPortForward)) {
                return $this->sshPortForward ? [
                    'sshUser' => null,
                    'sshHost' => null,
                    'sshPort' => null,
                ] : null;
            }

            return $this->sshPortForward;
        }
        $env = Environments::TESTCONTAINERS_SSH_FEEDFORWARDING();
        if (null !== $env) {
            return $this->parseSSHString($env);
        }

        return null;
    }

    /**
     * Parse an SSH string into its components.
     * The string should be in the format `[user@]host[:port]`.
     *
     * @param string $s the SSH string to parse
     *
     * @return array{
     *                sshUser?: string|null,
     *                sshHost?: string|null,
     *                sshPort?: int|null,
     *                }|null
     */
    private function parseSSHString($s)
    {
        $parts = explode('@', $s, 2);
        if (2 === count($parts)) {
            $sshUser = $parts[0];
            $parts = explode(':', $parts[1], 2);
            if (2 === count($parts)) {
                $sshHost = $parts[0];
                $sshPort = (int) $parts[1];
            } else {
                $sshHost = $parts[0];
                $sshPort = null;
            }
        } else {
            $sshUser = null;
            $parts = explode(':', $s, 2);
            if (2 === count($parts)) {
                $sshHost = $parts[0];
                $sshPort = (int) $parts[1];
            } else {
                $sshHost = $s;
                $sshPort = null;
            }
        }

        return [
            'sshUser' => $sshUser,
            'sshHost' => $sshHost,
            'sshPort' => $sshPort,
        ];
    }
}
