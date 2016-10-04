<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Query\Query;

class SelectQueryTest extends TestCase
{
    public function testProjection()
    {

        $query = new Query('SELECT * FROM users');
        $query->parse();

        $this->expectException(\InvalidArgumentException::class);
    }

}
