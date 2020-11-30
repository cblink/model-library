<?php

namespace Cblink\LaravelModelLibrary;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Trait ModelPageSizeTrait
 * @package App\Contracts\Traits
 * @method LengthAwarePaginator|static[] pageOrAll(array $column = ['*'], int $pageSize = 10, int $pageLimit = 500)
 * @method LengthAwarePaginator page(array $column = ['*'], int $pageSize = 10, int $maxPageSize = 150)
 * @method LengthAwarePaginator|static[] simplePageOrAll(array $column = ['*'], int $pageSize = 10, int $pageLimit = 500)
 * @method LengthAwarePaginator simplePage(array $column = ['*'], int $pageSize = 10, int $maxPageSize = 150)
 */
trait PageableTrait
{
    /**
     * 通过request参数来控制分页结果
     *
     * @param $query
     * @param array $column
     * @param int $pageSize
     * @param int $maxPageSize
     * @param bool $simple
     * @return LengthAwarePaginator
     */
    public function scopePage($query, $column = ['*'], int $pageSize = 10, int $maxPageSize = 150, bool $simple = false)
    {
        $size = request(config('app.paginate.page_key', 'page_size'), $pageSize);

        // 限定分页每页不大于150条
        $size = ($size > $maxPageSize) ? $maxPageSize : $size;

        $callMethod = $simple ? 'simplePaginate' : 'paginate';

        return $query->{$callMethod}($size, $column);
    }

    /**
     * 通过request参数来控制分页结果
     *
     * @param $query
     * @param array $column
     * @param int $pageSize
     * @param int $maxPageSize
     * @return LengthAwarePaginator
     */
    public function scopeSimplePage($query, $column = ['*'], int $pageSize = 10, int $maxPageSize = 150)
    {
        return $query->scopePage($query, $column, $pageSize, $maxPageSize, true);
    }

    /**
     * 获取分页或集合
     *
     * @param $query
     * @param array $column
     * @param int $pageSize
     * @param int $pageLimit
     * @param bool $simple
     * @return LengthAwarePaginator|static[]
     */
    public function scopePageOrAll($query, $column = ['*'], $pageSize = 10, $pageLimit = 500, bool $simple = false)
    {
        return request(config('app.paginate.all_key', 'is_all')) ? $query->limit($pageLimit)->get($column) : $this->scopePage($query, $column, $pageSize, $simple);
    }

    /**
     * 获取分页或集合，分页使用简单分页
     *
     * @param $query
     * @param array $column
     * @param int $pageSize
     * @param int $pageLimit
     * @return LengthAwarePaginator|static[]
     */
    public function scopeSimplePageOrAll($query, $column = ['*'], $pageSize = 10, $pageLimit = 500)
    {
        return $this->scopePageOrAll($query, $column, $pageSize, $pageLimit, true);
    }
}
