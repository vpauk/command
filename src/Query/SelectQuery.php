<?php

namespace Query;

use Config\Config;
use Database\Manager;

class SelectQuery implements QueryInterface
{

    /**
     * @var string
     */
    private $value;

    /**
     * Query constructor.
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = trim($value);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return \MongoDB\Driver\Cursor
     */
    public function execute()
    {
        $parser = SelectQueryParser::create($this->value)->parse();

        $collection = Config::get('name') . '.' . $parser->getTarget();

        $query = new \MongoDB\Driver\Query($parser->getCondition(), ['projection' => $parser->getProjection(), 'skip' => $parser->getSkip(), 'limit' => $parser->getLimit()]);

        return Manager::getInstance()->executeQuery($collection, $query);
    }
}