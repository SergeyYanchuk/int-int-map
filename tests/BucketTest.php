<?php

namespace Tests;

require_once 'MemDriver.php';
require_once 'Bucket.php';

use PHPUnit\Framework\TestCase;

class BucketTest extends TestCase
{
    private function getReadMock(int $offset = 0): \MemDriver
    {
        $driver = $this
            ->getMockBuilder(\MemDriver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['read'])
            ->getMock();

        $driver->expects($this->once())
            ->method('read')
            ->with(10 + $offset)
            ->willReturn(5);

        return $driver;
    }

    private function getWriteMock(int $offset = 0): \MemDriver
    {
        $driver = $this
            ->getMockBuilder(\MemDriver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['write'])
            ->getMock();

        $driver->expects($this->once())
            ->method('write')
            ->with(5, 10 + $offset)
            ->willReturn(8);

        return $driver;
    }

    public function testSetHash(): void
    {
        $bucket = new \Bucket(10, $this->getWriteMock());
        $bucket->setHash(5);
    }

    public function testGetHash(): void
    {
        $bucket = new \Bucket(10, $this->getReadMock());
        $bucket->getHash();
    }

    public function testSetKey(): void
    {
        $bucket = new \Bucket(10, $this->getWriteMock(1));
        $bucket->setKey(5);
    }

    public function testGetKey(): void
    {
        $bucket = new \Bucket(10, $this->getReadMock(1));
        $this->assertEquals(5, $bucket->getKey());
    }

    public function testSetValue(): void
    {
        $bucket = new \Bucket(10, $this->getWriteMock(2));
        $bucket->setValue(5);
    }

    public function testGetValue(): void
    {
        $bucket = new \Bucket(10, $this->getReadMock(2));
        $this->assertEquals(5, $bucket->getValue());
    }

    public function testSetNext(): void
    {
        $bucket = new \Bucket(10, $this->getWriteMock(3));
        $bucket->setNext(5);
    }

    public function testGetNext(): void
    {
        $bucket = new \Bucket(10, $this->getReadMock(3));
        $this->assertEquals(5, $bucket->getNext());
    }

    public function testIsEquals(): void
    {
        $driver = $this
            ->getMockBuilder(\MemDriver::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['read'])
            ->getMock();

        $driver->expects($this->exactly(3))
            ->method('read')
            ->with(11)
            ->willReturn(5);

        $bucket = new \Bucket(10, $driver);

        $this->assertTrue($bucket->isEquals(5));
        $this->assertFalse($bucket->isEquals(0));
        $this->assertFalse($bucket->isEquals(6));
    }
}