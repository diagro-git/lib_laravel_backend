<?php
namespace Diagro\Backend\Http\Resources;

trait GroupedResourceProperties
{

    public static array $grouped = [];


    protected function group(string $property, $value)
    {
        if(isset(self::$grouped[$property])) {
            self::$grouped[$property][] = $value;
        } else {
            self::$grouped[$property] = [$value];
        }

        return $value;
    }


    public function withResponse($request, $response)
    {
        foreach(self::$grouped as $property => $values) {
            if(method_exists($this, $property)) {
                $this->{$property}($values, $request, $response);
            }
        }
    }

}
