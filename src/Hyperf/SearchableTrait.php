<?php

namespace Cblink\ModelLibrary\Hyperf;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class ModelSearchTrait
 * @method $this search(array $items, array $attributes = [])
 * @package App\Http\Traits\Model
 */
trait SearchableTrait
{

    /**
     * @param $query
     * @param array $items
     * @param array $attributes
     * @return Builder
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
