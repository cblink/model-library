<?php

namespace Cblink\LaravelModelLibrary\Search;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder;

class SimpleSearch
{
    use SearchMethod;

    /**
     * @var Builder
     */
    protected $query;

    /**
     * @var array
     */
    protected $items;

    /**
     * @var array
     */
    protected $attributes = [];

    public function __construct($query, $items, $attributes = [])
    {
        $this->query = $query;
        $this->items = $items;
        $this->attributes = $attributes;
    }

    /**
     * @param null $key
     * @return array|\ArrayAccess|mixed
     */
    public function rules($key = null)
    {
        $rules = [
            // where($key, $val)
            'eq' => ['nullable', 'string', 'max:100'],
            // where($key, '<', $val)
            'lt' => ['nullable', 'integer'],
            // where($key, '<=', $val)
            'lte' => ['nullable', 'integer'],
            // where($key, '>', $val)
            'gt' => ['nullable', 'integer'],
            // where($key, '>=', $val)
            'gte' => ['nullable', 'integer'],
            // 使用whereDate筛选，示例值：2019-10-10~2020-12-10
            'date' => ['nullable', 'string', 'max:21', new DateSearchRule()],
            // 使用where筛选，示例值：2019-10-10 12:24:33~2020-12-10 15:33:22
            'datetime' => ['nullable', 'string', 'max:40', new DateSearchRule()],
            // 关键词筛选，LIKE搜索
            'keyword' => ['nullable', 'string', 'max:100'],
            // where in 筛选
            'in' => ['nullable', 'array'],
        ];

        if ($key) {
            return Arr::get($rules, $key);
        }

        return $rules;
    }

    /**
     * @return Builder
     */
    public function search()
    {
        if ($this->items) {
            $this->validate();

            $this->query->where(function (Builder $query) {
                $inputs = request()->only(array_keys($this->items));

                foreach ($inputs as $key => $input) {
                    $this->execQuery($query, $this->items[$key], $key, $input);
                }
            });
        }

        return $this->query;
    }

    /**
     * 进行过滤
     *
     * @param $query
     * @param array $rule
     * @param $key
     * @param $input
     */
    public function execQuery($query, array $rule, $key, $input)
    {
        $params = [
            'type' => 'eq',
            'relate' => null,
            'field' => $key,
            'default' => null
        ];

        $params = array_merge($params, $rule);

        // 调用的方法
        $callMethod = sprintf('where%s', ucfirst($params['type']));

        if ($params['relate']) {
            $query->whereHas($params['relate'], function ($query) use ($params, $callMethod, $input) {
                $this->callMethod($callMethod, [$query, $params['field'], $input]);
            });
        } else {
            $this->callMethod($callMethod, [$query, $params['field'], $input]);
        }
    }

    /**
     * 调方法
     *
     * @param $method
     * @param $args
     */
    public function callMethod($method, $args)
    {
        if (!method_exists($this, $method)) {
            $this->whereDefault(...$args);

            return;
        }

        call_user_func_array([$this, $method], $args);
    }


    public function validate()
    {
        $validate = validator(request()->all(), $this->getRules(), [], $this->attributes);

        if ($validate->fails()) {
            throw new InvalidArgumentException();
        }
    }

    /**
     * @return array
     */
    protected function getRules()
    {
        $rules = [];

        foreach ($this->items as $field => $params) {
            $type = $params['type'] ?? 'eq';

            if (!array_key_exists($type, $this->items)) {
                continue;
            }
            $rules[$field] = $this->rules($type);
        }

        return $rules;
    }

    // 默认筛选
    public function whereDefault(Builder $query, $key, $val)
    {
        $query->where($key, $val);
    }
}
