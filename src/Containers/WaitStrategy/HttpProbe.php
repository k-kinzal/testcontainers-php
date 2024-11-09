<?php

namespace Testcontainers\Containers\WaitStrategy;

interface HttpProbe
{
    public function available($endpoint, $responseCode = 200);
}
