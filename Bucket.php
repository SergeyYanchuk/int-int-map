<?php


class Bucket
{
    private int $startPosition;

    private MemDriver $driver;

    public function __construct(int $startPosition, MemDriver $driver)
    {
        $this->startPosition = $startPosition;
        $this->driver = $driver;
    }

    public function setHash(int $hash): void
    {
        $this->driver->write($hash, $this->startPosition);
    }

    public function getHash(): int
    {
        return $this->driver->read($this->startPosition);
    }

    public function setKey(int $key): void
    {
        $this->driver->write($key, $this->startPosition + 1);
    }

    public function getKey(): int
    {
        return $this->driver->read($this->startPosition + 1);
    }

    public function setValue(int $value)
    {
        $this->driver->write($value, $this->startPosition + 2);
    }

    public function getValue(): ?int
    {
        return $this->driver->read($this->startPosition + 2);
    }

    public function setNext(int $next)
    {
        $this->driver->write($next, $this->startPosition + 3);
    }

    public function getNext(): ?int
    {
        return $this->driver->read($this->startPosition + 3);
    }

    public function getNextBucket(): ?self
    {
        $next = $this->getNext();
        if ($next == null) {
            return null;
        }
        return new Bucket($next, $this->driver);
    }

    public function isEquals($key): bool
    {
        if ($this->getKey() === $key) {
            return true;
        }

        return false;
    }

    public function getEqualsOrLast($key): ?self
    {
        if ($this->isEquals($key)) {
            return $this;
        }

        $bucket = $this->getNextBucket();

        if ($bucket === null) {
            return $this;
        }

        return $bucket->getEqualsOrLast($key);
    }
}