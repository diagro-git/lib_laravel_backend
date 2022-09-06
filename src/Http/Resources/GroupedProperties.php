<?php
namespace Diagro\Backend\Http\Resources;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * This trait is used to group ID's which contains more information in another backend.
 */
trait GroupedProperties
{

    public static array $grouped = [];


    protected function group(string $property, $value)
    {
        if($value != null) {
            if (isset(self::$grouped[$property])) {
                if (!in_array($value, self::$grouped[$property])) {
                    self::$grouped[$property][] = $value;
                }
            } else {
                self::$grouped[$property] = [$value];
            }
        }

        return $value;
    }


    public function withResponse($request, $response)
    {
        $wrap = static::$wrap;
        $isCollection = str_ends_with(static::class, 'Collection');
        $grouped = self::$grouped;

        if($isCollection && method_exists($this, 'collects') && Arr::has(trait_uses_recursive($collects = $this->collects()), GroupedProperties::class)) {
            $grouped = array_merge($grouped, $collects::$grouped);
        }

        foreach($grouped as $property => $values) {
            $methodName = Str::camel($property);
            if(method_exists($this, $methodName)) {
                /** @var GroupedDefinition $definition */
                $definition = $this->{$methodName}();
                $isOneValue = count($values) == 1 && ! $isCollection;
                if($isOneValue) $values = $values[0];
                $collectorData = $definition->collector()($values);
                if(Arr::isAssoc($collectorData)) $collectorData = [$collectorData];
                $resourceData = $response->getData(true);
                if($wrap != null) $resourceData = $resourceData[$wrap];
                if(Arr::isAssoc($resourceData)) $resourceData = [$resourceData];
                $compare = $definition->compare();
                $compareIsString = is_string($compare);
                $compareIsClosure = ! $compareIsString;

                //compare and replace
                foreach($collectorData as $collectorItem) {
                    foreach($resourceData as $key => $resourceItem) {
                        if((isset($resourceItem[$property])) && (
                            ($compareIsString && $resourceItem[$property] == $collectorItem[$compare]) ||
                            ($compareIsClosure && $compare($resourceItem, $collectorItem) === true)
                            )) {
                            $resourceData[$key][$property] = $collectorItem;
                        }
                    }
                }

                //set data back in response
                if(! $isCollection && count($resourceData) == 1) {
                    $resourceData = $resourceData[0];
                }

                if($wrap == null) {
                    $response->setData($resourceData);
                } else {
                    $response->setData([$wrap => $resourceData]);
                }
            }
        }
    }

}
