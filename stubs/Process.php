<?php

namespace Symfony\Component\Process;

class Process
{
    /**
     * Symfony 3.4 yields stdout/stderr chunks keyed by Process::OUT or Process::ERR.
     *
     * @param int $flags
     * @return \Generator<string, string>
     */
    public function getIterator($flags = 0)
    {
    }
}
