<?php

namespace Cblink\ModelLibrary\Hyperf;

use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * @method \Hyperf\Database\Model\Builder|static whenWith(array $with = [], array $loaded = [])
 */
trait WhenWithTrait
{

    /**
     * with
     *
     * @param $query
     * @param array $with       懒加载的with条件 格式为 key => with内容
     * @param array $loaded     必须加载的with条件
     * @return mixed
     */
    public function scopeWhenWith($query, array $with = [], array $loaded = [])
    {
        $withQuery = make(RequestInterface::class)
            ->input(config('custom.paginate.with_key', 'with_query'), []);

        $data = $loaded;

        foreach ($withQuery as $key) {
            if (!array_key_exists($key, $with)) {
                continue;
            }

            // 如果是数组，则标记为with
            if (is_array($with[$key])) {
                $data = array_merge($data, $with[$key]);
                continue;
            }

            // 如果是闭包，则视为回调方法
            if ($with[$key] instanceof \Closure) {
                $query = call_user_func($with[$key], $query);
            }
        }

        return $query->with($data);
    }

}