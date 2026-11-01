<?php

namespace Testcontainers\SSH\Exceptions;

use RuntimeException;

/**
 * An SSH tunnel could not be established or maintained.
 */
final class TunnelException extends RuntimeException
{
}
