<?php

namespace Testcontainers\Containers\WaitStrategy;

/**
 * HttpProbeGetHeaders checks the availability of a specific HTTP endpoint by retrieving its headers.
 *
 * This class implements the HttpProbe interface and provides a method to check if the specified
 * HTTP endpoint is available by checking its response headers.
 */
class HttpProbeGetHeaders implements HttpProbe
{
    public function available($endpoint, $responseCode = 200)
    {
        $headers = @get_headers($endpoint);
        if (false === $headers) {
            return false;
        }
        if (!isset($headers[0])) {
            return false;
        }
        if (!is_string($headers[0])) {
            return false;
        }
        $statusCode = (int) substr($headers[0], 9, 3);

        return $statusCode == $responseCode;
    }
}
