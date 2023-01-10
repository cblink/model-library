<?php

namespace Cblink\ModelLibrary\Kernel;

trait SearchOrMethod
{
    /**
     * 日期筛选
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Hyperf\Database\Model\Builder $query
     * @param $key
     * @param $val
     */
    public function orWhereDate($query, $key, $val)
    {
        $date = explode("~", $val);
        if (count($date) == 2) {
            $query->orWhere(function($query) use ($key, $date){
                $query
                    ->when(!empty($date[0]), function ($query) use ($key, $date) {
                        $query->whereDate($key, '>=', strtodate($date[0], 'Y-m-d'));
                    })
                    ->when(!empty($date[1]), function ($query) use ($key, $date) {
                        $query->whereDate($key, '<=', strtodate($date[1], 'Y-m-d'));
                    });
            });
        } else {
            $query->orWhereDate($key, $date[0]);
        }
    }

    /**
     * 时间筛选
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Hyperf\Database\Model\Builder $query
     * @param $key
     * @param $val
     */
    public function orWhereDatetime($query, $key, $val)
    {
        $date = explode("~", $val);
        if (count($date) == 2) {
            $query->orWhere(function($query) use ($key, $date){
                $query
                    ->when(!empty($date[0]), function ($query) use ($key, $date) {
                        $query->where($key, '>=', strtodate($date[0]));
                    })
                    ->when(!empty($date[1]), function ($query) use ($key, $date) {
                        $query->where($key, '<=', strtodate($date[1]));
                    });
            });
        } else {
            $query->orWhere($key, $date[0]);
        }
    }

    /**
     * 关键字筛选
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Hyperf\Database\Model\Builder $query
     * @param $key
     * @param $val
     */
    public function orWhereKeyword($query, $key, $val)
    {
        $query->orWhere($key, 'LIKE', $val);
    }

    /**
     * where in
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Hyperf\Database\Model\Builder $query
     * @param $key
     * @param $val
     */
    public function orWhereIn($query, $key, $val)
    {
        $query->orWhereIn($key, is_array($val) ? $val : [$val]);
    }

    /**
     * 等于
     *
     * @param $query
     * @param $key
     * @param $val
     */
    public function orWhereEq($query, $key, $val)
    {
        $query->orWhere($key, '=', $val);
    }

    /**
     * 小于
     *
     * @param $query
     * @param $key
     * @param $val
     */
    public function orWhereLt($query, $key, $val)
    {
        $query->orWhere($key, '<', $val);
    }

    /**
     * 小于或等于
     *
     * @param $query
     * @param $key
     * @param $val
     */
    public function orWhereLte($query, $key, $val)
    {
        $query->orWhere($key, '<=', $val);
    }

    /**
     * 大于
     *
     * @param $query
     * @param $key
     * @param $val
     */
    public function orWhereGt($query, $key, $val)
    {
        $query->orWhere($key, '>', $val);
    }


    /**
     * 大于或等于
     *
     * @param $query
     * @param $key
     * @param $val
     */
    public function orWhereGte($query, $key, $val)
    {
        $query->orWhere($key, '>=', $val);
    }

}
