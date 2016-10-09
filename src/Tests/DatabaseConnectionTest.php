<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Database\Manager;

class DatabaseConnectionTest extends TestCase
{

    public function testConnection()
    {
        Manager::getInstance();

        $this->expectException(\MongoDB\Driver\Exception\InvalidArgumentException::class);
        $this->expectException(\MongoDB\Driver\Exception\RuntimeException::class);
    }

}
