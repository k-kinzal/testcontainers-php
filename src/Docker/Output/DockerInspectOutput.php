<?php

namespace Testcontainers\Docker\Output;

use LogicException;
use Symfony\Component\Process\Process;
use Testcontainers\Docker\Exception\InvalidValueException;
use Testcontainers\Docker\Types\ContainerObject;
use Testcontainers\Docker\Types\State;

/**
 * Represents the output of a Docker `inspect` command executed via Symfony Process.
 *
 * @property-read State $state
 */
class DockerInspectOutput extends DockerOutput
{
    /**
     * Object representation of the container.
     * @var ContainerObject $object
     */
    private $object;

    /**
     * @param Process $process The Symfony Process instance that executed the `docker inspect` command.
     */
    public function __construct($process)
    {
        parent::__construct($process);

        try {
            $this->object = $this->deserialize($process->getOutput());
        } catch (InvalidValueException $e) {
            throw new LogicException('Failed to deserialize Docker inspect output', 0, $e);
        }
    }

    /**
     * Deserialize the output of the `docker inspect` command.
     *
     * @param string $s The output of the `docker inspect` command.
     * @return ContainerObject The object representation of the container.
     *
     * @throws InvalidValueException If the output could not be parsed.
     */
    private function deserialize($s)
    {
        $output = json_decode($s, true);
        if ($output === null) {
            throw new InvalidValueException('Docker inspect output is not valid JSON', ['output' => $s]);
        }
        if (!is_array($output)) {
            throw new InvalidValueException('Docker inspect output is not an array', ['output' => $s]);
        }
        if (count($output) === 0) {
            throw new InvalidValueException('Docker inspect output is an empty array', ['output' => $s]);
        }
        return ContainerObject::fromArray($output[0]);
    }

    /**
     * Get the value of a property.
     *
     * @param string $name The name of the property.
     * @return mixed The value of the property.
     */
    public function __get($name)
    {
        if (!property_exists($this->object, $name)) {
            throw new LogicException("Property $name does not exist");
        }
        return $this->object->$name;
    }
}
