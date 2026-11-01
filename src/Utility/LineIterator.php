<?php

namespace Testcontainers\Utility;

use Symfony\Component\Process\Process;

/**
 * LineIterator iterates over the output of a process line by line.
 */
class LineIterator
{
    use Ensure;

    /**
     * The process to read the output from.
     *
     * @var Process
     */
    private $process;

    /**
     * @param Process $process the process to read the output from
     */
    public function __construct($process)
    {
        self::ensureInstanceOf($process, Process::class);

        $this->process = $process;
    }

    /**
     * Returns an iterator for the lines of the output.
     *
     * The flags and the keys are the same as `Process::getIterator()`.
     * In non-blocking mode, `null` is yielded when the output read at once does not complete a line.
     * The last line is yielded even if it does not end with a newline.
     *
     * @param int $flags a bit field of Process::ITER_* flags
     *
     * @return \Generator<string, null|string> an iterator to traverse the lines without newlines
     */
    public function getIterator($flags = 0)
    {
        self::ensureIntRange($flags, 0);

        $blocking = !(Process::ITER_NON_BLOCKING & $flags);
        $buffers = [Process::OUT => '', Process::ERR => ''];
        foreach ($this->process->getIterator($flags) as $type => $chunk) {
            $lines = explode("\n", $buffers[$type].$chunk);
            $buffers[$type] = array_pop($lines);
            foreach ($lines as $line) {
                yield $type => $line;
            }
            if (!$blocking && count($lines) === 0) {
                yield $type => null;
            }
        }
        foreach ($buffers as $type => $buffer) {
            if ($buffer !== '') {
                yield $type => $buffer;
            }
        }
    }
}
