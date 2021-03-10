<?php


namespace Tests;

require_once 'IntIntMap.php';

use PHPUnit\Framework\TestCase;

class IntIntMapTest extends TestCase
{
    private \Shmop $shmId;

    public function setUp(): void
    {
        parent::setUp();
        $shmKey = ftok(__FILE__, 't');
        $this->shmId = shmop_open($shmKey, "c", 0777, 300);

    }

    public function tearDown(): void
    {
        parent::tearDown();
        shmop_delete($this->shmId);
    }

    public function testMap()
    {
        $map = new \IntIntMap($this->shmId, 300);

        $map->put(23, 6);
        $map->put(22, 17);

        $this->assertEquals(17, $map->get(22));
        $this->assertEquals(6, $map->get(23));
    }

    public function testFullMap()
    {
        $map = new \IntIntMap($this->shmId, 300);

        $map->put(23, 6);
        $map->put(22, 17);
        $map->put(11, 3);
        $map->put(12, 3);
        $map->put(13, 3);
        $map->put(14, 3);
        $map->put(15, 3);
        $this->expectException(\IntIntMapException::class);
        $map->put(16, 3);
        $map->put(17, 3);
        $map->put(18, 3);
        $map->put(19, 3);
        $map->put(20, 3);
        $map->put(45, 3);
        $map->put(55, 3);
    }

    public function testMapRewrite()
    {
        $map = new \IntIntMap($this->shmId, 300);

        $this->assertNull($map->put(23, 6));
        $this->assertEquals(6, $map->put(23, 17));
    }
}