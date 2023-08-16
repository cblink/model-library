<?php

namespace Cblink\ModelLibrary\Laravel;

/**
 * @method static|\Illuminate\Database\Eloquent\Builder whenSort(array $sortKeys = [])
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
        $withSort = request(config('custom.paginate.sort_key', 'with_sort'), []);

        foreach ($withSort as $key => $sort) {
            if (!in_array($key, $sortKeys)) {
                continue;
            }

            $query->orderBy($key, $sort == 'asc' ? 'asc' : 'desc');
        }

        return $query;
    }

}