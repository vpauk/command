<?php

namespace Query;

class SelectQueryParser implements QueryParserInterface
{

    const LOGICAL_AND = 'AND';
    const LOGICAL_OR  = 'OR';
    const LOGICAL_XOR = 'XOR';

    const ORDER_ASC = 'ASC';
    const ORDER_DESC = 'DESC';

    public static $compareOperations = ['=' => '$eq', '<>' => '$ne', '>' => '$gt', '>=' => '$gte', '<' => '$lt', '<=' => '$lte'];

    /**
     * @var string
     */
    private $queryString;

    /**
     * @var string
     */
    private $projection;

    /**
     * @var string
     */
    private $target;

    /**
     * @var string
     */
    private $condition;

    /**
     * @var string
     */
    private $sort;

    /**
     * @var integer
     */
    private $skip;

    /**
     * @var integer
     */
    private $limit;

    /**
     * SelectQueryParser constructor.
     * @param string $queryString
     */
    public function __construct($queryString)
    {
        $this->queryString = $queryString;
    }

    /**
     * @param string $queryString
     * @return SelectQueryParser
     */
    public static function create($queryString)
    {
        return new SelectQueryParser($queryString);
    }

    /**
     * @return string
     */
    public function getProjection()
    {
        return $this->projection;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }


    /**
     * @return integer
     */
    public function getSkip()
    {
        return $this->skip;
    }

    /**
     * @return integer
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Parse query
     *
     * @return $this
     */
    public function parse()
    {
        if (!preg_match('/SELECT\s+(.+)\s+FROM\s+(.+)\s+WHERE\s+(.+)\s+GROUP\s+BY\s+(.+)\s+ORDER\s+BY\s+(.+)\s+([ASC|DESC]+)\s+SKIP\s+(\d+)\s+LIMIT\s+(\d+)/i', $this->queryString, $matches)) {
            throw new \InvalidArgumentException('Can not parse query!');
        }
        $this->projection = $this->parseProjection($matches[1]);
        $this->target = $this->parseTarget($matches[2]);
        $this->condition = $this->parseCondition($matches[3]);
        $this->sort = $this->parseSort($matches[5], $matches[6]);
        $this->skip = $this->parseSkip($matches[7]);
        $this->limit = $this->parseLimit($matches[8]);

        return $this;
    }

    /**
     * @param string $projection
     * @return array
     */
    private function parseProjection($projection)
    {
        $result = [];

        $projection = trim($projection);

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
     * @param string $target
     * @return string
     */
    private function parseTarget($target)
    {
        return trim($target);

    }

    /**
     * @param string $condition
     * @return array
     */
    private function parseCondition($condition)
    {
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
     * @param string $field
     * @param string $direction
     * @return array
     */
    private function parseSort($field, $direction)
    {
        $field = trim($field);
        $direction = trim($direction);
        if ($direction == self::ORDER_ASC) {
            $result[$field] = 1;
        } else {
            $result[$field] = -1;
        }
        return $result;
    }

    /**
     * @param string $skip
     * @return integer
     */
    private function parseSkip($skip)
    {
        return (int) trim($skip);
    }

    /**
     * @param string $limit
     * @return integer
     */
    private function parseLimit($limit)
    {
        return (int) trim($limit);
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