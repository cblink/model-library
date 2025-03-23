<?php

namespace Cblink\ModelLibrary\Hyperf;

use Hyperf\Collection\Arr;
use Hyperf\Collection\Collection;
use InvalidArgumentException;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class SimpleSearch extends \Cblink\ModelLibrary\Kernel\SimpleSearch
{
    public function validate()
    {
        $validate = make(ValidatorFactoryInterface::class)
            ->make(
                $this->attributes ?: make(RequestInterface::class)->all(),
                $this->getRules()
            );

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
        return Arr::only($this->attributes ?: make(RequestInterface::class)->all(), array_keys($this->items));
    }
}