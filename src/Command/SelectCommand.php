<?php

namespace Command;

use Query\QueryInterface;

class SelectCommand implements CommandInterface
{
    /**
     * @var Query
     */
    private $query;

    /**
     * SelectCommand constructor.
     * @param QueryInterface $query
     */
    public function __construct(QueryInterface $query)
    {
        $this->query = $query;
    }

    public function execute()
    {
        $rows = $this->query->execute();

        if (count($rows)) {
            foreach ($rows as $row) {
                echo json_encode((array)$row) . "\n";
            }
        } else {
            echo "Nothing found!\n";
        }
    }
}
