<?php


namespace Softelebyte\SelectHelper\ValueObjects;


class CaseValueObject
{
    const CASE = '(case ';
    const END = ' end)';

    /**
     * @var string
     */
    private $sql;

    public function __construct()
    {
        $this->sql = self::CASE;
    }

    public function add($condition, $value)
    {
        $this->sql .= ' when ' . $condition . ' then ' . $value;
        $this->end();
    }

    public function else($value)
    {
        $this->sql .= ' else ' . $value;
    }

    public function end():string
    {
        $this->sql = str_replace(self::END, '', $this->sql);
        $this->sql .= self::END;
        return $this->sql;
    }
}
