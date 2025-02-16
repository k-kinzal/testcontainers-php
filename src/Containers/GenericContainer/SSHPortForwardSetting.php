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
     * @var string|bool|null
     */
    protected static $SSH_PORT_FORWARD;

    /**
     * @var array{
     *     sshUser?: string|null,
     *     sshHost: string,
     *     sshPort?: int|null,
     * }|bool|null
     */
    private $sshPortForward;

    /**
     * Set up SSH port forwarding to the remote host that starts the container.
     *
     * @param string $sshHost The SSH host to forward to.
     * @param int|null $sshPort The SSH port to forward to.
     * @param string|null $sshUser The SSH user to use for the connection.
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
     *     sshUser?: string|null,
     *     sshHost?: string|null,
     *     sshPort?: int|null,
     * }|null
     */
    protected function sshPortForward()
    {
        if (self::$SSH_PORT_FORWARD !== null) {
            if (is_bool(self::$SSH_PORT_FORWARD)) {
                return self::$SSH_PORT_FORWARD ? [
                    'sshUser' => null,
                    'sshHost' => null,
                    'sshPort' => null,
                ] : null;
            } else {
                return $this->parseSSHString(self::$SSH_PORT_FORWARD);
            }
        }
        if ($this->sshPortForward !== null) {
            if (is_bool($this->sshPortForward)) {
                return $this->sshPortForward ? [
                    'sshUser' => null,
                    'sshHost' => null,
                    'sshPort' => null,
                ] : null;
            } else {
                return $this->sshPortForward;
            }
        }
        $env = Environments::TESTCONTAINERS_SSH_FEEDFORWARDING();
        if ($env !== null) {
            return $this->parseSSHString($env);
        }
        return null;
    }

    /**
     * Parse an SSH string into its components.
     * The string should be in the format `[user@]host[:port]`.
     *
     * @return array{
     *     sshUser?: string|null,
     *     sshHost?: string|null,
     *     sshPort?: int|null,
     * }|null
     */
    private function parseSSHString($s)
    {
        $parts = explode('@', $s, 2);
        if (count($parts) === 2) {
            $sshUser = $parts[0];
            $parts = explode(':', $parts[1], 2);
            if (count($parts) === 2) {
                $sshHost = $parts[0];
                $sshPort = (int) $parts[1];
            } else {
                $sshHost = $parts[0];
                $sshPort = null;
            }
        } else {
            $sshUser = null;
            $parts = explode(':', $s, 2);
            if (count($parts) === 2) {
                $sshHost = $parts[0];
                $sshPort = (int)$parts[1];
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
