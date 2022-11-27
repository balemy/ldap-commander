<?php

declare(strict_types=1);

namespace Balemy\LdapCommander;

final class Timer
{
    /**
     * @var float[]
     */
    private array $runningTimers = [];

    /**
     * @var float[]
     */
    private array $finishedTimers = [];

    public function start(string $name): void
    {
        $this->runningTimers[$name] = microtime(true);
    }

    public function stop(string $name): void
    {
        if (!array_key_exists($name, $this->runningTimers)) {
            throw new \InvalidArgumentException("There is no \"$name\" timer started");
        }

        $this->finishedTimers[$name] = microtime(true) - $this->runningTimers[$name];
        unset($this->runningTimers[$name]);
    }

    public function has(string $name): bool
    {
        return (array_key_exists($name, $this->runningTimers) || array_key_exists($name, $this->finishedTimers));
    }

    public function get(string $name): float
    {
        if (array_key_exists($name, $this->runningTimers)) {
            $this->stop($name);
        }

        if (!array_key_exists($name, $this->finishedTimers)) {
            throw new \InvalidArgumentException("There is no \"$name\" timer started");
        }

        return $this->finishedTimers[$name];
    }
}
