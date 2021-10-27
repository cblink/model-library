<?php

namespace Cblink\ModelLibrary\Hyperf;

/**
 * Class ModelSearchTrait
 * @method static|\Hyperf\Database\Model\Builder search(array $items, array $attributes = [])
 * @package App\Http\Traits\Model
 */
trait SearchableTrait
{

    /**
     * @param $query
     * @param array $items
     * @param array $attributes
     * @return \Hyperf\Database\Model\Builder
     */
    public function scopeSearch($query, array $items, array $attributes = [])
    {
        return make(SimpleSearch::class, [
            'query' => $query,
            'items' => $items,
            'attributes' => $attributes,
        ])->search();
    }
}
