<?php

namespace Cblink\ModelLibrary\Kernel;

trait SearchMethod
{
    /**
     * 日期筛选
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Hyperf\Database\Model\Builder $query
     * @param $key
     * @param $val
     */
    public function whereDate($query, $key, $val)
    {
        $date = explode("~", $val);
        if (count($date) == 2) {
            $query
                ->when(!empty($date[0]), function ($query) use ($key, $date) {
                    $query->whereDate($key, '>=', strtodate($date[0], 'Y-m-d'));
                })
                ->when(!empty($date[1]), function ($query) use ($key, $date) {
                    $query->whereDate($key, '<=', strtodate($date[1], 'Y-m-d'));
                });
        } else {
            $query->whereDate($key, $date[0]);
        }
    }

    /**
     * 时间筛选
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Hyperf\Database\Model\Builder $query
     * @param $key
     * @param $val
     */
    public function whereDatetime($query, $key, $val)
    {
        $date = explode("~", $val);
        if (count($date) == 2) {
            $query
                ->when(!empty($date[0]), function ($query) use ($key, $date) {
                    $query->where($key, '>=', strtodate($date[0]));
                })
                ->when(!empty($date[1]), function ($query) use ($key, $date) {
                    $query->where($key, '<=', strtodate($date[1]));
                });
        } else {
            $query->where($key, $date[0]);
        }
    }

    /**
     * 关键字筛选
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Hyperf\Database\Model\Builder $query
     * @param $key
     * @param $val
     */
    public function whereKeyword($query, $key, $val)
    {
        $query->where($key, 'LIKE', $val);
    }

    /**
     * 关联结果查询
     *
     * @param $query
     * @param $key
     * @param $val
     * @return void
     */
    public function whereHas($query, $key, $val)
    {
        if (!in_array($val, [0, 1])) {
            return $query;
        }

        $query->{$val == 1 ? 'has' : 'whereDoesntHave'}($key);
    }

    /**
     * where in
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Hyperf\Database\Model\Builder $query
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
