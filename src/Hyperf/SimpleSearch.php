<?php

namespace Cblink\ModelLibrary\Hyperf;

use Hyperf\Utils\Arr;
use Hyperf\Utils\Collection;
use InvalidArgumentException;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

class SimpleSearch extends \Cblink\ModelLibrary\Kernel\SimpleSearch
{
    public function validate()
    {
        $validate = make(ValidatorFactoryInterface::class)
            ->make(
                make(RequestInterface::class)->all(),
                $this->getRules(),
                [],
                $this->attributes
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
        return Arr::only(make(RequestInterface::class)->all(), array_keys($this->items));
    }
}