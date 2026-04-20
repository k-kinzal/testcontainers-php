<?php

namespace Testcontainers\Containers\WaitStrategy;

use function Testcontainers\ensure;

/**
 * HttpProbeGetHeaders checks the availability of a specific HTTP endpoint by retrieving its headers.
 *
 * This class implements the HttpProbe interface and provides a method to check if the specified
 * HTTP endpoint is available by checking its response headers.
 */
class HttpProbeGetHeaders implements HttpProbe
{
    /**
     * {@inheritDoc}
     */
    public function available($endpoint, $responseCode = 200)
    {
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_string($endpoint), '$endpoint must be string');
        /** @psalm-suppress RedundantConditionGivenDocblockType */
        ensure(is_int($responseCode), '$responseCode must be int');

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
