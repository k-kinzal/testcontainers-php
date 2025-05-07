<?php

namespace Testcontainers\Docker\Output;

use Symfony\Component\Process\Process;
use Testcontainers\Docker\Exception\InvalidValueException;
use Testcontainers\Docker\Types\ContainerObject;
use Testcontainers\Docker\Types\State;

/**
 * Represents the output of a Docker `inspect` command executed via Symfony Process.
 *
 * @property State $state
 */
class DockerInspectOutput extends DockerOutput
{
    /**
     * Object representation of the container.
     *
     * @var ContainerObject
     */
    private $object;

    /**
     * @param Process $process the Symfony Process instance that executed the `docker inspect` command
     */
    public function __construct($process)
    {
        parent::__construct($process);

        try {
            $this->object = $this->deserialize($process->getOutput());
        } catch (InvalidValueException $e) {
            $this->object = ContainerObject::fromArray([
                'State' => [
                    'Status' => 'dead',
                    'ExitCode' => 255,
                ],
            ]);
        }
    }

    /**
     * Get the value of a property.
     *
     * @param string $name the name of the property
     *
     * @return mixed the value of the property
     */
    public function __get($name)
    {
        if (!property_exists($this->object, $name)) {
            throw new \LogicException("Property {$name} does not exist");
        }

        return $this->object->{$name};
    }

    /**
     * Deserialize the output of the `docker inspect` command.
     *
     * @param string $s the output of the `docker inspect` command
     *
     * @throws InvalidValueException if the output could not be parsed
     *
     * @return ContainerObject the object representation of the container
     */
    private function deserialize($s)
    {
        $output = json_decode($s, true);
        if (null === $output) {
            throw new InvalidValueException('Docker inspect output is not valid JSON', ['output' => $s]);
        }
        if (!is_array($output)) {
            throw new InvalidValueException('Docker inspect output is not an array', ['output' => $s]);
        }
        if (0 === count($output)) {
            throw new InvalidValueException('Docker inspect output is an empty array', ['output' => $s]);
        }
        if (!is_array($output[0])) {
            throw new InvalidValueException('Docker inspect output is not an array of objects', ['output' => $s]);
        }

        return ContainerObject::fromArray($output[0]);
    }
}
