<?php
namespace Diagro\Backend\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;


abstract class DiagroResource extends JsonResource
{

    use DiagroResourceFields;


    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->initFields();
    }


    public function toArray($request)
    {
        $data = $this->toData($request);
        if(empty($data)) {
            $data = parent::toArray($request);
        }

        $resource = $this->resource;
        if($resource instanceof Model) {
            $dbname = $resource->getConnection()->getDatabaseName();
            $table = $resource->getTable();
            $key = $resource->getKey();
            if(! empty($dbname) && ! empty($table) && ! empty($key)) {
                if (is_array($key)) {
                    $key = implode('.', $key);
                } elseif (! is_string($key)) {
                    $key = (string)$key;
                }
                CachedResource::addUsedResource($dbname, $table, $key);
            }
        }

        return $data;
    }


    public static function collection($resource)
    {
        $class = str_replace('Resource', 'Collection', static::class);
        if(class_exists($class)) {
            $collection = new $class($resource);
            if($collection instanceof ResourceCollection) {
                return $collection;
            }
        }

        //AnonymousResourceCollection if there is no Collection class.
        return parent::collection($resource);
    }


    /**
     * Use this if the resource is part of another API resource.
     *
     * @param $resource
     * @return mixed
     */
    public static function subResponse($resource): mixed
    {
        $original_wrap = static::$wrap;
        static::withoutWrapping();
        $data = (new static($resource))->toResponse(request())->getData(true);
        static::wrap($original_wrap);
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


}