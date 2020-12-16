<?php

namespace Cblink\LaravelModelLibrary\Search;

use Illuminate\Database\Eloquent\Builder;

trait SearchMethod
{
    /**
     * 日期筛选
     *
     * @param Builder $query
     * @param $key
     * @param $val
     */
    public function whereDate($query, $key, $val)
    {
        $date = explode("~", $val);
        if (count($date) == 2) {
            $query->whereDate($key, '>=', strtodate($date[0], 'Y-m-d'))
                ->whereDate($key, '<=', strtodate($date[1], 'Y-m-d'));
        } else {
            $query->whereDate($key, $date[0]);
        }
    }

    /**
     * 时间筛选
     *
     * @param Builder $query
     * @param $key
     * @param $val
     */
    public function whereDatetime($query, $key, $val)
    {
        $date = explode("~", $val);
        if (count($date) == 2) {
            $query->where($key, '>=', strtodate($date[0]))
                ->where($key, '<=', strtodate($date[1]));
        } else {
            $query->where($key, $date[0]);
        }
    }

    /**
     * 关键字筛选
     *
     * @param Builder $query
     * @param $key
     * @param $val
     */
    public function whereKeyword($query, $key, $val)
    {
        $query->where($key, 'LIKE', $val);
    }

    /**
     * where in
     *
     * @param Builder $query
     * @param $key
     * @param $val
     */
    public function whereIn($query, $key, $val)
    {
        $query->whereIn($key, is_array($val) ? $val : [$val]);
    }

    /**
     * 等于
     *
     * @param $query
     * @param $key
     * @param $val
     */
    public function whereEq($query, $key, $val)
    {
        $query->where($key, '=', $val);
    }

    /**
     * 小于
     *
     * @param $query
     * @param $key
     * @param $val
     */
    public function whereLt($query, $key, $val)
    {
        $query->where($key, '<', $val);
    }

    /**
     * 小于或等于
     *
     * @param $query
     * @param $key
     * @param $val
     */
    public function whereLte($query, $key, $val)
    {
        $query->where($key, '<=', $val);
    }

    /**
     * 大于
     *
     * @param $query
     * @param $key
     * @param $val
     */
    public function whereGt($query, $key, $val)
    {
        $query->where($key, '>', $val);
    }


    /**
     * 大于或等于
     *
     * @param $query
     * @param $key
     * @param $val
     */
    public function whereGte($query, $key, $val)
    {
        $query->where($key, '>=', $val);
    }

}
