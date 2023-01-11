<?php

namespace Cblink\ModelLibrary\Kernel;

use Illuminate\Support\Arr;

abstract class SimpleSearch
{
    use SearchMethod, SearchOrMethod;

    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Hyperf\Database\Model\Builder
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

    public function __construct($query, $items, array $attributes = [])
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
            'date' => ['nullable', 'string', 'max:21', $this->getDateRule()],
            // 使用where筛选，示例值：2019-10-10 12:24:33~2020-12-10 15:33:22
            'datetime' => ['nullable', 'string', 'max:40', $this->getDateRule()],
            // 关键词筛选，LIKE搜索
            'keyword' => ['nullable', 'string', 'max:100'],
            // where in 筛选
            'in' => ['nullable', 'array'],
            // has查询
            'has' => ['nullable', 'string'],
        ];

        if ($key) {
            return Arr::get($rules, $key);
        }

        return $rules;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Hyperf\Database\Model\Builder
     */
    public function search()
    {
        if ($this->items) {
            $this->validate();

            foreach ($this->getInputs() as $collects) {
                $this->query->where(function ($query) use ($collects){
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

        $inputs = $this->getInputData();

        foreach ($this->items as $key => $rules) {
            // 多维数据
            if ($rules && is_array(array_values($rules)[0])) {
                foreach ($rules as $rule) {
                    if (!isset($rule['group'])) {
                        $rule['group'] = $key;
                    }

                    if (is_null($result = $this->getRule($key, $inputs, $rule))) {
                        continue;
                    }

                    if (is_null($result['relate'])) {
                        $data[] = $result;
                        continue;
                    }

                    if (! array_key_exists($result['relate'], $data)) {
                        $data[$result['relate']] = $result;
                        continue;
                    }

                    $data[$result['relate']]['fields'][] = $result;
                }
            } else {

                if (is_null($result = $this->getRule($key, $inputs, $rules))) {
                    continue;
                }

                if (is_null($result['relate'])) {
                    $data[] = $result;
                    continue;
                }

                if (! array_key_exists($result['relate'], $data)) {
                    $data[$result['relate']] = $result;
                    continue;
                }

                $data[$result['relate']]['fields'][] = $result;
            }
        }

        $data = class_exists("\Hyperf\Utils\Collection") ?
            new \Hyperf\Utils\Collection($data) :
            new \Illuminate\Support\Collection($data);

        return $data->filter()->groupBy('group');
    }

    /**
     * @param $key
     * @param $inputs
     * @param $rules
     * @return array
     */
    public function getRule($key, $inputs, $rules)
    {
        $value = $rules['value'] ?? $inputs[$key] ?? $rules['default'] ?? null;

        if (
            is_null($value) ||
            (is_array($value) && count($value) == 0) ||
            (is_string($value) && trim($value) == "")
        ) {
            return null;
        }

        if (isset($rules['type'])) {
            if ($rules['type'] == 'keyword') {
                $rules['after'] = "%";
                $value = preg_replace("/[\^%_\[\]]/", '', $value);
            }

            if ($rules['type'] != 'in') {
                $value = ($rules['before'] ?? '') . $value . ($rules['after'] ?? '');
            }
        }

        $params = [
            'type' => 'eq',
            'relate' => null,
            'field' => $key,
            'default' => null,
            'group' => 'default',
            'mix' => 'and',
            "value" => $value,
            'fields' => [],
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
            'orWhereHas' : 'whereHas';

        $query->{$hasMethod}($params['relate'], function ($query) use ($params, $method) {
            $this->callMethod($method, [$query, $params['field'], $params['value']]);

            foreach ($params['fields'] as $item) {
                // 调用的方法
                $method = $item['mix'] == 'or' ?
                    sprintf('orWhere%s', ucfirst($item['type'])) :
                    sprintf('where%s', ucfirst($item['type']));

                $this->callMethod($method, [$query, $item['field'], $item['value']]);
            }
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
    public function whereDefault($query, $key, $val)
    {
        $query->where($key, $val);
    }

    /**
     * @return mixed
     */
    abstract public function validate();

    abstract public function getDateRule();

    abstract public function getInputData();
}
