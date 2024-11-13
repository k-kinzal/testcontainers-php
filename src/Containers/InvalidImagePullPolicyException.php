<?php

namespace Testcontainers\Containers;

use Exception;

class InvalidImagePullPolicyException extends Exception
{
    public function __construct($policy, $code = 0, $previous = null)
    {
        parent::__construct("Invalid image pull policy: $policy", $code, $previous);
    }
}
