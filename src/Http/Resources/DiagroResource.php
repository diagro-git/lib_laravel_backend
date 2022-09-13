<?php
namespace Diagro\Backend\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\MissingValue;


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
            $collection = new $class(...func_get_args());
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
        //if main response is a collection, then the next value is added to the grouped array.
        //this causes a bug and thus after the sub response, the array needs to be reset.
        if(property_exists(static::class, 'grouped')) {
            static::$grouped = [];
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


    /**
     * Only add field when access is allowed for given permission(s).
     *
     * @param string|array $abillities
     * @param string|array $permissions
     * @param $value
     * @return MissingValue|mixed
     */
    public function whenCan(string|array $abillities, string|array $permissions, $value): mixed
    {
        return $this->when(auth()->user()->can($abillities, $permissions), $value);
    }


    /**
     * Only add field when read access is allowed for given permission(s).
     *
     * @param string|array $permissions
     * @param $value
     * @return mixed
     */
    public function whenCanRead(string|array $permissions, $value): mixed
    {
        return $this->whenCan('read', $permissions, $value);
    }


    /**
     * Only add field when create access is allowed for given permission(s).
     *
     * @param string|array $permissions
     * @param $value
     * @return mixed
     */
    public function whenCanCreate(string|array $permissions, $value): mixed
    {
        return $this->whenCan('create', $permissions, $value);
    }


    /**
     * Only add field when update access is allowed for given permission(s).
     *
     * @param string|array $permissions
     * @param $value
     * @return mixed
     */
    public function whenCanUpdate(string|array $permissions, $value): mixed
    {
        return $this->whenCan('update', $permissions, $value);
    }


    /**
     * Only add field when delete access is allowed for given permission(s).
     *
     * @param string|array $permissions
     * @param $value
     * @return mixed
     */
    public function whenCanDelete(string|array $permissions, $value): mixed
    {
        return $this->whenCan('delete', $permissions, $value);
    }


    /**
     * Only add field when CRUD access is allowed for given permission(s).
     *
     * @param string|array $permissions
     * @param $value
     * @return mixed
     */
    public function whenCanCRUD(string|array $permissions, $value): mixed
    {
        return $this->whenCan(['c','r','u','d'], $permissions, $value);
    }


    /**
     * Only add field when publish access is allowed for given permission(s).
     *
     * @param string|array $permissions
     * @param $value
     * @return mixed
     */
    public function whenCanPublish(string|array $permissions, $value): mixed
    {
        return $this->whenCan('publish', $permissions, $value);
    }


    /**
     * Only add field when export access is allowed for given permission(s).
     *
     * @param string|array $permissions
     * @param $value
     * @return mixed
     */
    public function whenCanExport(string|array $permissions, $value): mixed
    {
        return $this->whenCan('export', $permissions, $value);
    }


}