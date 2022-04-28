<?php
namespace Diagro\Backend\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * header X-FIELDS=fieldname, fieldname, ....
 */
abstract class DiagroResource extends JsonResource
{

    protected array $fields = [];

    protected int $countFields = 0;


    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->initFields();
    }


    protected function field($name, $value)
    {
        return $this->when($this->countFields == 0 || in_array($name, $this->fields), $value);
    }


    protected function initFields()
    {
        $fields = request()->header('x-fields');
        if(! empty($fields)) {
            $fields = explode(',', $fields);
            foreach($fields as $k => $v) {
                $fields[$k] = trim($v);
            }

            $this->fields = $fields;
            $this->countFields = count($fields);
        }
    }


    final public function toArray($request)
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
            if(is_array($key)) {
                $key = implode('.', $key);
            } elseif(is_int($key)) {
                $key = (string) $key;
            }
            CachedResource::addUsedResource($dbname, $table, $key);
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
     * The fields with values for the JSON response.
     * Replaces the toArray method, but is used in the toArray method.
     *
     * @param $request
     * @return array
     */
    abstract function toData($request): array;


}