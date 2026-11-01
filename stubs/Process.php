<?php

namespace Symfony\Component\Process;

use Generator;
use IteratorAggregate;

/**
 * Symfony Process 3.4 signatures used when checking PHP 5.6 compatibility.
 *
 * @implements IteratorAggregate<string, string>
 */
class Process implements IteratorAggregate
{
    const OUT = 'out';
    const ERR = 'err';
    const ITER_NON_BLOCKING = 1;
    const ITER_KEEP_OUTPUT = 2;
    const ITER_SKIP_OUT = 4;
    const ITER_SKIP_ERR = 8;

    /**
     * Declare the Process 3.4 __construct contract.
     *
     * @param  string|array   $commandline the commandline argument
     * @param  null|string    $cwd         the cwd argument
     * @param  null|array     $env         the env argument
     * @param  mixed          $input       the input argument
     * @param  null|float|int $timeout     the timeout argument
     * @param  null|array     $options     the options argument
     * @return void
     */
    public function __construct($commandline, $cwd = null, array $env = null, $input = null, $timeout = 60, array $options = null)
    {
    }

    /**
     * Declare the Process 3.4 getIterator contract.
     *
     * @param  int                       $flags the flags argument
     * @return Generator<string, string>
     */
    public function getIterator($flags = 0)
    {
    }

    /**
     * Declare the Process 3.4 run contract.
     *
     * @param  null|callable $callback the callback argument
     * @param  array         $env      the env argument
     * @return int
     */
    public function run($callback = null, array $env = [])
    {
    }

    /**
     * Declare the Process 3.4 start contract.
     *
     * @param  null|callable $callback the callback argument
     * @param  array         $env      the env argument
     * @return void
     */
    public function start($callback = null, array $env = [])
    {
    }

    /**
     * Declare the Process 3.4 wait contract.
     *
     * @param  null|callable $callback the callback argument
     * @return int
     */
    public function wait($callback = null)
    {
    }

    /**
     * Declare the Process 3.4 stop contract.
     *
     * @param  float|int $timeout the timeout argument
     * @param  null|int  $signal  the signal argument
     * @return null|int
     */
    public function stop($timeout = 10, $signal = null)
    {
    }

    /**
     * Declare the Process 3.4 signal contract.
     *
     * @param  int  $signal the signal argument
     * @return self
     */
    public function signal($signal)
    {
    }

    /**
     * Declare the Process 3.4 setTimeout contract.
     *
     * @param  null|float|int $timeout the timeout argument
     * @return self
     */
    public function setTimeout($timeout)
    {
    }

    /**
     * Declare the Process 3.4 setIdleTimeout contract.
     *
     * @param  null|float|int $timeout the timeout argument
     * @return self
     */
    public function setIdleTimeout($timeout)
    {
    }

    /**
     * Declare the Process 3.4 setInput contract.
     *
     * @param  mixed $input the input argument
     * @return self
     */
    public function setInput($input)
    {
    }

    /**
     * Declare the Process 3.4 setOptions contract.
     *
     * @param  array $options the options argument
     * @return self
     */
    public function setOptions(array $options)
    {
    }

    /**
     * Declare the Process 3.4 getOutput contract.
     *
     * @return string
     */
    public function getOutput()
    {
    }

    /**
     * Declare the Process 3.4 getErrorOutput contract.
     *
     * @return string
     */
    public function getErrorOutput()
    {
    }

    /**
     * Declare the Process 3.4 getIncrementalOutput contract.
     *
     * @return string
     */
    public function getIncrementalOutput()
    {
    }

    /**
     * Declare the Process 3.4 getIncrementalErrorOutput contract.
     *
     * @return string
     */
    public function getIncrementalErrorOutput()
    {
    }

    /**
     * Declare the Process 3.4 getCommandLine contract.
     *
     * @return string
     */
    public function getCommandLine()
    {
    }

    /**
     * Declare the Process 3.4 getStatus contract.
     *
     * @return string
     */
    public function getStatus()
    {
    }

    /**
     * Declare the Process 3.4 isSuccessful contract.
     *
     * @return bool
     */
    public function isSuccessful()
    {
    }

    /**
     * Declare the Process 3.4 isRunning contract.
     *
     * @return bool
     */
    public function isRunning()
    {
    }

    /**
     * Declare the Process 3.4 isStarted contract.
     *
     * @return bool
     */
    public function isStarted()
    {
    }

    /**
     * Declare the Process 3.4 isTerminated contract.
     *
     * @return bool
     */
    public function isTerminated()
    {
    }

    /**
     * Declare the Process 3.4 isOutputDisabled contract.
     *
     * @return bool
     */
    public function isOutputDisabled()
    {
    }

    /**
     * Declare the Process 3.4 getExitCode contract.
     *
     * @return null|int
     */
    public function getExitCode()
    {
    }

    /**
     * Declare the Process 3.4 getPid contract.
     *
     * @return null|int
     */
    public function getPid()
    {
    }

    /**
     * Declare the Process 3.4 getTimeout contract.
     *
     * @return null|float
     */
    public function getTimeout()
    {
    }

    /**
     * Declare the Process 3.4 getIdleTimeout contract.
     *
     * @return null|float
     */
    public function getIdleTimeout()
    {
    }

    /**
     * Declare the Process 3.4 getWorkingDirectory contract.
     *
     * @return null|string
     */
    public function getWorkingDirectory()
    {
    }

    /**
     * Declare the Process 3.4 getExitCodeText contract.
     *
     * @return null|string
     */
    public function getExitCodeText()
    {
    }

    /**
     * Declare the Process 3.4 disableOutput contract.
     *
     * @return self
     */
    public function disableOutput()
    {
    }

    /**
     * Declare the Process 3.4 enableOutput contract.
     *
     * @return self
     */
    public function enableOutput()
    {
    }

    /**
     * Declare the Process 3.4 clearOutput contract.
     *
     * @return self
     */
    public function clearOutput()
    {
    }

    /**
     * Declare the Process 3.4 clearErrorOutput contract.
     *
     * @return self
     */
    public function clearErrorOutput()
    {
    }

    /**
     * Declare the Process 3.4 checkTimeout contract.
     *
     * @return void
     */
    public function checkTimeout()
    {
    }

    /**
     * Start a new process with the same command and configuration.
     *
     * @param  null|callable $callback the output callback
     * @param  array         $env      environment overrides
     * @return self
     */
    public function restart($callback = null, array $env = [])
    {
    }
}
