<?php
namespace Diagro\Backend\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class DiagroResourceCollection extends ResourceCollection
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
            }
            CachedResource::addUsedResource($dbname, $table, $key);
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