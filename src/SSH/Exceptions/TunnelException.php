<?php

namespace Testcontainers\SSH\Exceptions;

use RuntimeException;

/**
 * An SSH tunnel could not be established or maintained.
 *
 * @api
 */
class TunnelException extends RuntimeException
{
}
