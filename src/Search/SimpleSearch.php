<?php

namespace Cblink\LaravelModelLibrary\Search;

use Illuminate\Support\Arr;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder;

class SimpleSearch
{
    use SearchMethod, SearchOrMethod;

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

            $inputs = $this->getInputs();

            foreach (collect($inputs)->groupBy('group') as $collects) {
                $this->query->where(function (Builder $query) use ($collects){
                    foreach ($collects as $params) {
                        $this->execQuery($query, $params);
                    }
                });
            }
        }

        return $this->query;
    }

    /**
     * @return array|array[]
     */
    public function getInputs()
    {
        $data = [];

        $inputs = request()->only(array_keys($this->items));

        foreach ($inputs as $key => $val) {

            $rules = array_values($this->items[$key]);

            // 多维数据
            if ($rules && is_array($rules[0])) {
                foreach ($rules as $rule) {
                    if (!isset($rule['group'])) {
                        $rule['group'] = $key;
                    }
                    array_push($data, $this->getRule($key, $val, $rule));
                }
            } else {
                array_push($data, $this->getRule($key, $val, $rules));
            }
        }

        return $data;
    }

    /**
     * @param $key
     * @param $value
     * @param $rules
     * @return array
     */
    public function getRule($key, $value, $rules)
    {
        if (isset($rules['type']) && $rules['type'] == 'keyword') {
            $rules['after'] = "%";
            $value = trim($value, '%');
        }

        $value = ($rules['before'] ?? '') . $value . ($rules['after'] ?? '');

        $params = [
            'type' => 'eq',
            'relate' => null,
            'field' => $key,
            'default' => null,
            'group' => 'default',
            'mix' => 'and',
            "value" => $value,
        ];

        return array_merge($params, $rules);
    }

    /**
     * 进行过滤
     *
     * @param $query
     * @param array $params
     */
    public function execQuery($query, array $params = [])
    {
        // 调用的方法
        $method = $params['mix'] == 'or' ?
            sprintf('orWhere%s', ucfirst($params['type'])) :
            sprintf('where%s', ucfirst($params['type']));

        if ($params['relate']) {
            $this->callHasMethod($method, $query, $params);
        } else {
            $this->callMethod($method, [$query, $params['field'], $params['value']]);
        }
    }

    /**
     * @param $method
     * @param $query
     * @param $params
     */
    public function callHasMethod($method, $query, $params)
    {
        $hasMethod = $params['mix'] == 'or' ?
            'orWhereHas' : 'whereHash';

        $query->{$hasMethod}($params['relate'], function ($query) use ($params, $method) {
            $this->callMethod($method, [$query, $params['field'], $params['value']]);
        });
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
