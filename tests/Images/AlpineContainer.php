<?php

namespace Tests\Images;

use Testcontainers\Containers\GenericContainer\GenericContainer;

final class AlpineContainer extends GenericContainer
{
    public static $IMAGE = 'alpine:latest';

    public static $COMMANDS = [
        'tail',
        '-f',
        '/dev/null',
    ];
}
