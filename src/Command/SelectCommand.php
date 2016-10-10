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
        try {
            $rows = $this->query->execute();
            $i = 0;
            foreach ($rows as $row) {
                echo json_encode((array)$row) . "\n";
                $i++;
            }
            if ($i === 0) {
                echo "Nothing found!\n";
            }
        } catch (\Exception $e) {
            echo 'Caught exception: ', $e->getMessage(), "\n";
        }
    }
}
