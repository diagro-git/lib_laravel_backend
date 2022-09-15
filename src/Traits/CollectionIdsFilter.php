<?php
namespace Diagro\Backend\Traits;

use Illuminate\Support\Collection;

/**
 * Filters a given collection that matches given ids.
 */
trait CollectionIdsFilter
{

    /**
     * Filters the given items with ID's from request query.
     * If the parameter $ids is a string, it takes the ID's from request query with $ids as name.
     *
     * Items must be objects containing 'id' as property.
     *
     * @param Collection $items
     * @param array|string $ids
     * @return Collection
     */
    protected function filterIds(Collection $items, array|string $ids = 'ids'): Collection
    {
        if(is_string($ids)) {
            $ids = request()->query($ids);
        }

        if($ids == null || ! is_array($ids)) {
            return $items;
        }

        return $items->filter(fn($item) => in_array($item->id, $ids));
    }

}