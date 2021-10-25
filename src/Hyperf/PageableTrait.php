<?php

namespace Cblink\ModelLibrary\Hyperf;

use Hyperf\Paginator\Paginator;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * Trait ModelPageSizeTrait
 * @package App\Contracts\Traits
 * @method Paginator|static[] pageOrAll(array $column = ['*'], int $pageSize = 10, int $pageLimit = 500)
 * @method Paginator page(array $column = ['*'], int $pageSize = 10, int $maxPageSize = 150)
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
     * @return Paginator
     */
    public function scopePage($query, array $column = ['*'], int $pageSize = 10, int $maxPageSize = 150, bool $simple = false)
    {
        $size = make(RequestInterface::class)->input(config('custom.paginate.page_key', 'per_page'), $pageSize);

        // 限定分页每页不大于150条
        $size = ($size > $maxPageSize) ? $maxPageSize : $size;

        return $query->paginate($size, $column);
    }

    /**
     * 获取分页或集合
     *
     * @param $query
     * @param array $column
     * @param int $pageSize
     * @param int $pageLimit
     * @param int $maxPageSize
     * @param bool $simple
     * @return Paginator|static[]
     */
    public function scopePageOrAll($query, array $column = ['*'], int $pageSize = 10, int $pageLimit = 500, int $maxPageSize = 150, bool $simple = false)
    {
        return make(RequestInterface::class)->input(config('custom.paginate.all_key', 'is_all')) ?
            $query->limit($pageLimit)->get($column) :
            $this->scopePage($query, $column, $pageSize, $maxPageSize, $simple);
    }
}
