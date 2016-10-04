<?php

namespace Query;

use Config\Config;
use Database\Manager;

class SelectQuery implements QueryInterface
{
    const SELECT = 'SELECT';
    const FROM = 'FROM';
    const WHERE = 'WHERE';

    const LOGICAL_AND = 'AND';
    const LOGICAL_OR  = 'OR';
    const LOGICAL_XOR = 'XOR';

    public static $compareOperations = ['=' => '$eq', '<>' => '$ne', '>' => '$gt', '>=' => '$gte', '<' => '$lt', '<=' => '$lte'];

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
        $projection = $this->parseProjection();
        $target = $this->parseTarget();
        $condition = $this->parseCondition();
        print_r($condition);

        $collection = Config::get('name') . '.' . $target;

        $query = new \MongoDB\Driver\Query($condition, ['projection' => $projection, 'sort' => [ 'firstname' => 1], 'limit' => 1000]);

        return Manager::getInstance()->executeQuery($collection, $query);
    }

    /**
     * @return array
     */
    private function parseProjection()
    {
        if (strpos($this->value, self::FROM) === false) {
            throw new \InvalidArgumentException('Can not parse projection');
        }

        if (!preg_match('/^'.self::SELECT.'(.+)'.self::FROM.'/i', $this->value, $matches)) {
            throw new \InvalidArgumentException('Can not parse projection');
        }

        if (!isset($matches[1])) {
            throw new \InvalidArgumentException('Can not parse projection');
        }
        $result = [];

        $projection = trim($matches[1]);

        if ($projection === '*') {
            return $result;
        }

        $list = explode(',', $projection);
        foreach ($list as $item) {
            $item = trim($item);
            $result[$item] = true;
        }

        return $result;
    }

    /**
     * @return string
     */
    private function parseTarget()
    {
        if (!preg_match('/^'.self::SELECT.'.+'.self::FROM.'\s*([^-\s]+)\s*/i', $this->value, $matches)) {
            return '';
        }
        return isset($matches[1]) ? $matches[1] : '';

    }

    /**
     * @return array
     */
    private function parseCondition()
    {
        if (!preg_match('/^'.self::SELECT.'.+'.self::FROM.'\s*.+\s*'.self::WHERE.'\s*(.+)/i', $this->value, $matches)) {
            return [];
        }
        $condition = $matches[1];
        $conditions = [];
        if (strpos($condition, self::LOGICAL_AND)) {
            $andParts = explode(self::LOGICAL_AND, $condition);
            foreach ($andParts as $item) {
                $conditions['$and'][] = $this->convertCondition(trim($item));

            }
        } elseif (strpos($condition, self::LOGICAL_OR)) {
            $orParts = explode(self::LOGICAL_OR, $condition);
            foreach ($orParts as $item) {
                $conditions['$or'][] = $this->convertCondition(trim($item));

            }
        } elseif (strpos($condition, self::LOGICAL_XOR)) {
            $xorParts = explode(self::LOGICAL_XOR, $condition);
            foreach ($xorParts as $item) {
                $conditions['$xor'][] = $this->convertCondition(trim($item));
            }
        } else {
            $conditions = $this->convertCondition(trim($condition));
        }
        return $conditions;

    }

    /**
     * @param string $condition
     * @return array
     */
    private function convertCondition($condition)
    {
        $convertedCondition = [];
        foreach (self::$compareOperations as $key => $value) {
            if (strpos($condition, $key) !== false) {
                $parts = explode($key, $condition);
                $fieldName = trim($parts[0]);
                $fieldValue = trim($parts[1]);
                $convertedCondition[$fieldName] = [];
                $convertedCondition[$fieldName][$value] = str_replace(['\'', '"'], '', $fieldValue);
            }
        }
        return $convertedCondition;
    }

}