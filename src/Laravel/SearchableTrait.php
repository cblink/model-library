<?php

namespace Cblink\ModelLibrary\Laravel;

/**
 * Class ModelSearchTrait
 * @method static|\Illuminate\Database\Eloquent\Builder search(array $items, array $attributes = [])
 * @package App\Http\Traits\Model
 */
trait SearchableTrait
{

    /**
     * @param $query
     * @param array $items
     * @param array $attributes
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, array $items, array $attributes = [])
    {
        return app(SimpleSearch::class, [
            'query' => $query,
            'items' => $items,
            'attributes' => $attributes,
        ])->search();
    }
}
