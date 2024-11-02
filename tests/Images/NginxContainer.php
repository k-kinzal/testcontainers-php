<?php

namespace Tests\Images;

use Testcontainers\Containers\GenericContainer;

class NginxContainer extends GenericContainer
{
    public static $IMAGE = 'nginx:1.27.2';

    public static $PORTS = [80];

    public static $PORT_STRATEGY = 'local_random';
}
