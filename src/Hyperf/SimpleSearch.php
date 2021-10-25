<?php

namespace Cblink\ModelLibrary\Hyperf;

use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use InvalidArgumentException;

class SimpleSearch extends \Cblink\ModelLibrary\Kernel\SimpleSearch
{
    public function validate()
    {
        $validate = make(ValidatorFactoryInterface::class)->make(request()->all(), $this->getRules(), [], $this->attributes);

        if ($validate->fails()) {
            throw new InvalidArgumentException();
        }
    }

    public function getDateRule()
    {
        return new DateSearchRule();
    }
}