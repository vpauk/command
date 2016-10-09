<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Database\Manager;

class DatabaseConnectionTest extends TestCase
{

    public function testConnection()
    {
        try {
            Manager::getInstance();
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

}
