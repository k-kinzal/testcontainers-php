<?php

namespace Testcontainers\SSH;

use Symfony\Component\Process\Process;
use Testcontainers\Logger\WithLogger;
use Testcontainers\SSH\Exceptions\TunnelException;

/**
 * A tunnel for forwarding connections over SSH.
 * @api
 */
class Tunnel
{
    use WithLogger;

    /**
     * The local port to bind to.
     *
     * @var int
     * @psalm-var mixed
     */
    protected $localPort;

    /**
     * The remote host to connect to.
     *
     * @var string
     * @psalm-var mixed
     */
    protected $remoteHost;

    /**
     * The remote port to connect to.
     *
     * @var int
     * @psalm-var mixed
     */
    protected $remotePort;

    /**
     * The SSH host to connect to.
     *
     * @var string
     * @psalm-var mixed
     */
    protected $sshHost;

    /**
     * The user to connect as.
     *
     * @var null|string
     * @psalm-var mixed
     */
    protected $user;

    /**
     * The SSH port to connect to.
     *
     * @var null|int
     * @psalm-var mixed
     */
    protected $sshPort;

    /**
     * The local bind address.
     *
     * @var null|string
     * @psalm-var mixed
     */
    protected $localBindAddress;

    /**
     * The identity file to use.
     *
     * @var null|string
     * @psalm-var mixed
     */
    protected $identityFile;

    /**
     * The SSH options to use.
     *
     * @var array<string, string>
     * @psalm-var mixed
     */
    protected $sshOptions = [];

    /**
     * Create a Tunnel instance.
     *
     * @param int    $localPort  the local port to bind to
     * @param string $remoteHost the remote host to connect to
     * @param int    $remotePort the remote port to connect to
     * @param string $sshHost    the SSH host to connect to
     * @psalm-param mixed $localPort
     * @psalm-param mixed $remoteHost
     * @psalm-param mixed $remotePort
     * @psalm-param mixed $sshHost
     */
    public function __construct(
        $localPort,
        $remoteHost,
        $remotePort,
        $sshHost
    ) {
        self::ensureIntOrDigitStringRange($localPort, 1, 65535);
        self::ensureNonEmptyString($remoteHost);
        self::ensureIntOrDigitStringRange($remotePort, 1, 65535);
        self::ensureNonEmptyString($sshHost);

        $localPort = (int) $localPort;
        $remotePort = (int) $remotePort;

        $this->localPort = $localPort;
        $this->remoteHost = $remoteHost;
        $this->remotePort = $remotePort;
        $this->sshHost = $sshHost;
    }

    /**
     * Set the user to connect as.
     *
     * @param string $user the user to connect as
     * @psalm-param mixed $user
     *
     * @return self
     */
    public function withUser($user)
    {
        self::ensureString($user);

        $this->user = $user;

        return $this;
    }

    /**
     * Set the SSH port to connect to.
     *
     * @param int $sshPort the SSH port to connect to
     * @psalm-param mixed $sshPort
     *
     * @return self
     */
    public function withSshPort($sshPort)
    {
        self::ensureIntOrDigitStringRange($sshPort, 1, 65535);

        $sshPort = (int) $sshPort;

        $this->sshPort = $sshPort;

        return $this;
    }

    /**
     * Set the local bind address.
     *
     * @param string $localBindAddress the local bind address
     * @psalm-param mixed $localBindAddress
     *
     * @return self
     */
    public function withLocalBindAddress($localBindAddress)
    {
        self::ensureString($localBindAddress);

        $this->localBindAddress = $localBindAddress;

        return $this;
    }

    /**
     * Set the identity file to use.
     *
     * @param string $identityFile the identity file to use
     * @psalm-param mixed $identityFile
     *
     * @return self
     */
    public function withIdentityFile($identityFile)
    {
        self::ensureString($identityFile);

        $this->identityFile = $identityFile;

        return $this;
    }

    /**
     * Set the SSH options to use.
     *
     * @param array<string, string> $sshOptions the SSH options to use
     * @psalm-param mixed $sshOptions
     *
     * @return self
     */
    public function withSshOptions($sshOptions)
    {
        self::ensureStringMap($sshOptions);

        $this->sshOptions = $sshOptions;

        return $this;
    }

    /**
     * Add an SSH option to use.
     *
     * @param string $option the SSH option to use
     * @param string $value  the value of the SSH option
     * @psalm-param mixed $option
     * @psalm-param mixed $value
     *
     * @return self
     */
    public function withSshOption($option, $value)
    {
        self::ensureString($option);
        self::ensureString($value);
        self::ensureStringMap($this->sshOptions);

        $options = $this->sshOptions;

        $options[$option] = $value;
        $this->sshOptions = $options;

        return $this;
    }

    /**
     * Open the tunnel.
     *
     * @return Session
     */
    public function open()
    {
        self::ensureIntOrDigitStringRange($this->localPort, 1, 65535);
        self::ensureIntOrDigitStringRange($this->remotePort, 1, 65535);
        self::ensureNonEmptyString($this->remoteHost);
        self::ensureNonEmptyString($this->sshHost);
        self::ensureNullableString($this->user);
        self::ensureNullableString($this->localBindAddress);
        self::ensureNullableString($this->identityFile);
        self::ensureStringMap($this->sshOptions);

        if ($this->sshPort !== null) {
            self::ensureIntOrDigitStringRange($this->sshPort, 1, 65535);

            $this->sshPort = (int) $this->sshPort;
        }

        $this->localPort = (int) $this->localPort;
        $this->remotePort = (int) $this->remotePort;

        $commandParts = [
            'ssh',
            '-v',
            '-o',
            'ExitOnForwardFailure=yes',
            '-N',
            '-L',
        ];
        if ($this->localBindAddress !== null && $this->localBindAddress !== '') {
            $commandParts[] = $this->localBindAddress.':'.$this->localPort.':'.$this->remoteHost.':'.$this->remotePort;
        } else {
            $commandParts[] = $this->localPort.':'.$this->remoteHost.':'.$this->remotePort;
        }
        if ($this->user !== null && $this->user !== '') {
            $commandParts[] = $this->user.'@'.$this->sshHost;
        } else {
            $commandParts[] = $this->sshHost;
        }
        if ($this->sshPort !== null) {
            $commandParts[] = '-p';
            $commandParts[] = $this->sshPort;
        }
        if ($this->identityFile !== null && $this->identityFile !== '') {
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
                $lastLine = implode("\n", array_slice(explode("\n", $err), -1));

                throw new TunnelException('Failed to start SSH tunnel: '.implode(' ', $commandParts).': '.$lastLine);
            }
            if (strpos($process->getErrorOutput(), 'Connection established.') !== false) {
                break;
            }
            usleep(100);
        }

        return new Session($process);
    }
}
