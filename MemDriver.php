<?php

class MemDriver
{
    private \Shmop $resource;
    private int $size;

    public function __construct(\Shmop $shm_id, int $size)
    {
        $this->resource = $shm_id;
        $this->size = $size;
    }

    public function read(int $offset): int
    {
        return (int) \shmop_read ($this->resource, $offset * \PHP_INT_SIZE,\PHP_INT_SIZE);
    }

    public function write(int $value, int $offset): int
    {
        return \shmop_write($this->resource, (string) $value, $offset  * \PHP_INT_SIZE);
    }
}