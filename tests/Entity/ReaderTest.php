<?php
namespace App\Tests\Entity;

use App\Entity\Reader;
use PHPUnit\Framework\TestCase;

class ReaderTest extends TestCase
{
    protected Reader $entity;

    public function setUp(): void
    {
        $this->entity = new Reader();
    }

    public function testNull(): void
    {
        $this->assertNull($this->entity->getId());
        $this->assertNull($this->entity->getName());
    }

    public function testSettersAndGetters(): void
    {
        $this->entity->setName('TestName');
        $this->assertEquals('TestName', $this->entity->getName());
    }
}