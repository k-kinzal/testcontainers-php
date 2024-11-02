<?php

namespace Tests\Images;

use Testcontainers\Containers\GenericContainer;

class AlpineContainer extends GenericContainer
{
    public static $IMAGE = 'alpine:latest';
}
