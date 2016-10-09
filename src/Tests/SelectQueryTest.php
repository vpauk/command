<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Query\SelectQuery;

class SelectQueryTest extends TestCase
{

    public function testProjection()
    {
        $this->expectException(\InvalidArgumentException::class);

        $query = new SelectQuery("SELECT firstname, lastname, email FROM users WHERE firstname = 'Volodymyr' OR firstname = '111111' GROUP BY none ORDER BY email ASC SKIP 2 LIMIT 10");
        $query->execute();
    }

}
