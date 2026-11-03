<?php

namespace Tests\Images;

use Testcontainers\Containers\GenericContainer\GenericContainer;

final class AlpineContainer extends GenericContainer
{
    public static $IMAGE = 'alpine:latest';

    protected static $STOP_TIMEOUT = 0;

    public static $COMMANDS = [
        'tail',
        '-f',
        '/dev/null',
    ];
}
