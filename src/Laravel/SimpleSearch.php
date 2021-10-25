<?php

namespace Cblink\ModelLibrary\Laravel;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder;

class SimpleSearch extends \Cblink\ModelLibrary\Kernel\SimpleSearch
{
    public function validate()
    {
        $validate = validator(request()->all(), $this->getRules(), [], $this->attributes);

        if ($validate->fails()) {
            throw new InvalidArgumentException();
        }
    }

    public function getDateRule()
    {
        return new DateSearchRule();
    }
}