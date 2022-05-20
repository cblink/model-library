<?php

namespace Cblink\ModelLibrary\Hyperf;

/**
 * @method \Hyperf\Database\Model\Builder|static whenWith($with)
 */
trait WhenWithTrait
{

    /**
     * with
     *
     * @param $query
     * @param array $with
     * @return mixed
     */
    public function scopeWhenWith($query, array $with = [])
    {
        $withQuery = make(RequestInterface::class)
            ->input(config('custom.paginate.with_key', 'with_query'), []);

        $data = [];

        foreach ($withQuery as $key) {
            if (array_key_exists($key, $with)) {
                $data += $with[$key];
            }
        }

        return $query->with($data);
    }

}