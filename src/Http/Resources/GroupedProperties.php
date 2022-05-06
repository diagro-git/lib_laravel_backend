<?php
namespace Diagro\Backend\Http\Resources;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Als je een property hebt in je API Resource dat een API fetch moet doen naar de backend,
 * dan kan je deze best groeperen.
 *
 * Na het maken van de API resource, worden de gegroepeerde waarde (meestal is dat de ID van een model) meegegeven
 * in een functie met de groepsnaam. Bv: $this->group('company', $company_id), gaan alle company_id's aan de methode company() gegeven worden.
 *
 * function company();
 *
 * Dan kan je een request doen, met het resultaat dan de bestaande data key vervangen door het API resultaat.
 * Dit kan resulteren in één API request ipv 100 API requests als je API resource bv 100 entries heeft.
 */
trait GroupedProperties
{

    public static array $grouped = [];


    protected function group(string $property, $value)
    {
        if(isset(self::$grouped[$property])) {
            if(! in_array($value, self::$grouped[$property])) {
                self::$grouped[$property][] = $value;
            }
        } else {
            self::$grouped[$property] = [$value];
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
                $resourceData = $response->getData(true)[$wrap];
                if(Arr::isAssoc($resourceData)) $resourceData = [$resourceData];
                $compare = $definition->compare();
                $compareIsString = is_string($compare);
                $compareIsClosure = ! $compareIsString;

                //compare and replace
                foreach($collectorData as $collectorItem) {
                    foreach($resourceData as $key => $resourceItem) {
                        if(
                            ($compareIsString && $resourceItem[$property] == $collectorItem[$compare]) ||
                            ($compareIsClosure && $compare($resourceItem, $collectorItem) === true)
                        ) {
                            $resourceData[$key][$property] = $collectorItem;
                        }
                    }
                }

                //set data back in response
                if(! $isCollection && count($resourceData) == 1) {
                    $resourceData = $resourceData[0];
                }
                $response->setData([$wrap => $resourceData]);
            }
        }
    }

}
