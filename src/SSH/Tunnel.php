<?php

namespace Testcontainers\SSH;

use Symfony\Component\Process\Process;
use Testcontainers\SSH\Exceptions\TunnelException;
use Testcontainers\Utility\WithLogger;

/**
 * A tunnel for forwarding connections over SSH.
 */
class Tunnel
{
    use WithLogger;

    /**
     * The local port to bind to.
     *
     * @var int
     */
    protected $localPort;

    /**
     * The remote host to connect to.
     *
     * @var string
     */
    protected $remoteHost;

    /**
     * The remote port to connect to.
     *
     * @var int
     */
    protected $remotePort;

    /**
     * The SSH host to connect to.
     *
     * @var string
     */
    protected $sshHost;

    /**
     * The user to connect as.
     *
     * @var null|string
     */
    protected $user;

    /**
     * The SSH port to connect to.
     *
     * @var null|int
     */
    protected $sshPort;

    /**
     * The local bind address.
     *
     * @var null|string
     */
    protected $localBindAddress;

    /**
     * The identity file to use.
     *
     * @var null|string
     */
    protected $identityFile;

    /**
     * The SSH options to use.
     *
     * @var array<string, string>
     */
    protected $sshOptions = [];

    /**
     * @param int    $localPort  the local port to bind to
     * @param string $remoteHost the remote host to connect to
     * @param int    $remotePort the remote port to connect to
     * @param string $sshHost    the SSH host to connect to
     */
    public function __construct(
        $localPort,
        $remoteHost,
        $remotePort,
        $sshHost
    ) {
        $this->localPort = $localPort;
        $this->remoteHost = $remoteHost;
        $this->remotePort = $remotePort;
        $this->sshHost = $sshHost;
    }

    /**
     * Set the user to connect as.
     *
     * @param string $user the user to connect as
     *
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
     * @param int $sshPort the SSH port to connect to
     *
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
     * @param string $localBindAddress the local bind address
     *
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
     * @param string $identityFile the identity file to use
     *
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
     * @param array<string, string> $sshOptions the SSH options to use
     *
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
     * @param string $option the SSH option to use
     * @param string $value  the value of the SSH option
     *
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
            $commandParts[] = $this->localBindAddress.':'.$this->localPort.':'.$this->remoteHost.':'.$this->remotePort;
        } else {
            $commandParts[] = $this->localPort.':'.$this->remoteHost.':'.$this->remotePort;
        }
        if ($this->user) {
            $commandParts[] = $this->user.'@'.$this->sshHost;
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
            $commandParts[] = $option.'='.$value;
        }
        $this->logger()->debug(implode(' ', $commandParts), [
            'sshHost' => $this->sshHost,
            'sshPort' => $this->sshPort,
            'remoteHost' => $this->remoteHost,
            'remotePort' => $this->remotePort,
            'localPort' => $this->localPort,
            'localBindAddress' => $this->localBindAddress,
            'identityFile' => $this->identityFile,
            'sshOptions' => $this->sshOptions,
        ]);
        $process = new Process($commandParts);
        $process->start();

        $time = time();
        while (true) {
            if (time() - $time > 10) {
                $process->stop();

                throw new TunnelException('Failed to start SSH tunnel: '.implode(' ', $commandParts).': Timed out');
            }
            if (!$process->isRunning()) {
                $err = trim($process->getErrorOutput());
                $lastLine = substr($err, strrpos($err, "\n") + 1);

                throw new TunnelException('Failed to start SSH tunnel: '.implode(' ', $commandParts).': '.$lastLine);
            }
            if (false !== strpos($process->getErrorOutput(), 'Connection established.')) {
                break;
            }
            usleep(100);
        }

        return new Session($process);
    }
}
