<?php

namespace Testcontainers\SSH;

use Symfony\Component\Process\Process;
use Testcontainers\SSH\Exceptions\TunnelException;

/**
 * A tunnel for forwarding connections over SSH.
 */
class Tunnel
{
    /**
     * The local port to bind to.
     * @var int
     */
    protected $localPort;

    /**
     * The remote host to connect to.
     * @var string
     */
    protected $remoteHost;

    /**
     * The remote port to connect to.
     * @var int
     */
    protected $remotePort;

    /**
     * The SSH host to connect to.
     * @var string
     */
    protected $sshHost;

    /**
     * The user to connect as.
     * @var string|null
     */
    protected $user = null;

    /**
     * The SSH port to connect to.
     * @var int|null
     */
    protected $sshPort = null;

    /**
     * The local bind address.
     * @var string|null
     */
    protected $localBindAddress = null;

    /**
     * The identity file to use.
     * @var string|null
     */
    protected $identityFile = null;

    /**
     * The SSH options to use.
     * @var array<string, string>
     */
    protected $sshOptions = [];

    /**
     * @param int $localPort The local port to bind to.
     * @param string $remoteHost The remote host to connect to.
     * @param int $remotePort The remote port to connect to.
     * @param string $sshHost The SSH host to connect to.
     */
    public function __construct(
        $localPort,
        $remoteHost,
        $remotePort,
        $sshHost
    ) {
        $this->localPort  = $localPort;
        $this->remoteHost = $remoteHost;
        $this->remotePort = $remotePort;
        $this->sshHost    = $sshHost;
    }

    /**
     * Set the user to connect as.
     *
     * @param string $user The user to connect as.
     * @return self
     */
    public function withUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Set the SSH port to connect to.
     *
     * @param int $sshPort The SSH port to connect to.
     * @return self
     */
    public function withSshPort($sshPort)
    {
        $this->sshPort = $sshPort;

        return $this;
    }

    /**
     * Set the local bind address.
     *
     * @param string $localBindAddress The local bind address.
     * @return self
     */
    public function withLocalBindAddress($localBindAddress)
    {
        $this->localBindAddress = $localBindAddress;
        return $this;
    }

    /**
     * Set the identity file to use.
     *
     * @param string $identityFile The identity file to use.
     * @return self
     */
    public function withIdentityFile($identityFile)
    {
        $this->identityFile = $identityFile;

        return $this;
    }

    /**
     * Set the SSH options to use.
     *
     * @param array<string, string> $sshOptions The SSH options to use.
     * @return self
     */
    public function withSshOptions($sshOptions)
    {
        $this->sshOptions = $sshOptions;

        return $this;
    }

    /**
     * Add an SSH option to use.
     *
     * @param string $option The SSH option to use.
     * @param string $value The value of the SSH option.
     * @return self
     */
    public function withSshOption($option, $value)
    {
        $this->sshOptions[$option] = $value;

        return $this;
    }

    /**
     * Open the tunnel.
     *
     * @return Session
     */
    public function open()
    {
        $commandParts = [
            'ssh',
            '-v',
            '-o',
            'ExitOnForwardFailure=yes',
            '-N',
            '-L',
        ];
        if ($this->localBindAddress) {
            $commandParts[] = $this->localBindAddress . ':' . $this->localPort . ':' . $this->remoteHost . ':' . $this->remotePort;
        } else {
            $commandParts[] = $this->localPort . ':' . $this->remoteHost . ':' . $this->remotePort;
        }
        if ($this->user) {
            $commandParts[] = $this->user . '@' . $this->sshHost;
        } else {
            $commandParts[] = $this->sshHost;
        }
        if ($this->sshPort) {
            $commandParts[] = '-p';
            $commandParts[] = $this->sshPort;
        }
        if ($this->identityFile) {
            $commandParts[] = '-i';
            $commandParts[] = $this->identityFile;
        }
        foreach ($this->sshOptions as $option => $value) {
            $commandParts[] = '-o';
            $commandParts[] = $option . '=' . $value;
        }
        $process = new Process($commandParts);
        $process->start();

        $time = time();
        while (true) {
            if (time() - $time > 10) {
                $process->stop();
                throw new TunnelException('Failed to start SSH tunnel: ' . implode(' ', $commandParts) . ': Timed out');
            }
            if (!$process->isRunning()) {
                $err = trim($process->getErrorOutput());
                $lastLine = substr($err, strrpos($err, "\n") + 1);
                throw new TunnelException('Failed to start SSH tunnel: ' . implode(' ', $commandParts) . ': ' . $lastLine);
            }
            if (strpos($process->getErrorOutput(), 'Connection established.') !== false) {
                break;
            }
            usleep(100);
        }

        return new Session($process);
    }
}
