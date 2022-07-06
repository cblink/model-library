<?php

namespace Cblink\ModelLibrary\Hyperf;

use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * @method \Hyperf\Database\Model\Builder|static whenSort(array $sortKeys = [])
 */
trait WhenSortTrait
{
    /**
     * with
     *
     * @param $query
     * @param array $sortKeys 允许传入控制的排序字段
     * @return mixed
     */
    public function scopeWhenSort($query, array $sortKeys = [])
    {
        $withSort = make(RequestInterface::class)
            ->input(config('custom.paginate.sort_key', 'with_sort'), []);

        foreach ($withSort as $key => $sort) {
            if (!in_array($key, $sortKeys)) {
                continue;
            }

            $query->orderBy($key, $sort == 'asc' ? 'asc' : 'desc');
        }

        return $query;
    }

}