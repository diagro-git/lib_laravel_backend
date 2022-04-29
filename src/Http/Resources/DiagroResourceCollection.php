<?php
namespace Diagro\Backend\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

abstract class DiagroResourceCollection extends ResourceCollection
{

    use DiagroResourceFields;


    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->initFields();
    }


    final public function toArray($request)
    {
        $data = $this->toData($request);
        if(empty($data)) {
            $data = parent::toArray($request);
        }

        $resource = $this->resource;
        if($resource instanceof Collection) {
            $class = str_replace('Collection', 'Resource', static::class);
            foreach($resource as $item) {
                if($item instanceof $class && $item->resource instanceof Model) {
                    $dbname = $item->resource->getConnection()->getDatabaseName();
                    $table = $item->resource->getTable();
                    $key = $item->resource->getKey();
                    if (is_array($key)) {
                        $key = implode('.', $key);
                    }
                    CachedResource::addUsedResource($dbname, $table, $key);
                }
            }
        }

        return $data;
    }


    /**
     * The fields with values for the JSON response.
     * Replaces the toArray method, but is used in the toArray method.
     *
     * @param $request
     * @return array
     */
    abstract function toData($request): array;


    public static function collection($resource)
    {
        throw new \BadMethodCallException("Static method collection can't be called on a resource collection class!");
    }


}