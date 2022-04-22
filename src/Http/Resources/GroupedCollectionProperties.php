<?php
namespace Diagro\Backend\Http\Resources;

use Illuminate\Support\Arr;

trait GroupedCollectionProperties
{

    public function withResponse($request, $response)
    {
        $collects = $this->collects();
        if(Arr::has(trait_uses_recursive($collects), GroupedResourceProperties::class)) {
            foreach($collects::$grouped as $property => $values) {
                if(method_exists($this, $property)) {
                    $this->{$property}($values, $request, $response);
                }
            }
        }
    }

}
