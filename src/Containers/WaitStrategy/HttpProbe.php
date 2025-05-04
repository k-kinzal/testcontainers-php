<?php

namespace Testcontainers\Containers\WaitStrategy;

/**
 * HttpProbe interface defines the contract for strategies that check the availability of a specific HTTP endpoint.
 * Implementations of this interface should provide the logic to determine if an HTTP endpoint is reachable.
 */
interface HttpProbe
{
    /**
     * Checks if the specified HTTP endpoint is available.
     *
     * @param string $endpoint     the HTTP endpoint to check
     * @param int    $responseCode the expected HTTP response code (default is 200)
     *
     * @return bool true if the endpoint is available, false otherwise
     */
    public function available($endpoint, $responseCode = 200);
}
