<?php

namespace Softelebyte\SoftelebyteJoins\Mixins;

class QueryBuilderExtraMethods
{
    public function getGroupBy()
    {
        return function () {
            return $this->groups;
        };
    }

    public function getSelect()
    {
        return function () {
            return $this->columns;
        };
    }
}
