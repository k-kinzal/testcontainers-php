<?php

namespace Testcontainers\Containers\WaitStrategy;

class HttpProbeGetHeaders implements HttpProbe
{
    public function available($endpoint, $responseCode = 200)
    {
        $headers = @get_headers($endpoint);
        if ($headers === false) {
            return false;
        }
        if (!isset($headers[0])) {
            return false;
        }
        $statusCode = (int) substr($headers[0], 9, 3);
        return $statusCode == $responseCode;
    }
}
