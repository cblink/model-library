<?php

namespace Cblink\ModelLibrary\Laravel;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder;

class SimpleSearch extends \Cblink\ModelLibrary\Kernel\SimpleSearch
{
    public function validate()
    {
        $validate = validator($this->attributes ?: request()->all(), $this->getRules(), []);

        if ($validate->fails()) {
            throw new InvalidArgumentException();
        }
    }

    public function getDateRule()
    {
        return new DateSearchRule();
    }

    public function getInputData()
    {
        $keys = array_keys($this->items);

        if ($this->attributes) {
            return Arr::only($this->attributes, $keys);
        }
        return request()->only($keys);
    }
}