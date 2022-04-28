<?php
namespace Diagro\Backend\Http\Resources;

/**
 * Als je een property hebt in je API Resource dat een API fetch moet doen naar de backend,
 * dan kan je deze best groeperen.
 *
 * Na het maken van de API resource, worden de gegroepeerde waarde (meestal is dat de ID van een model) meegegeven
 * in een functie met de groepsnaam. Bv: $this->group('company', $company_id), gaan alle company_id's aan de methode company() gegeven worden.
 *
 * function company(array $ids, $request, $response);
 *
 * Dan kan je een request doen, met het resultaat dan de bestaande data key vervangen door het API resultaat.
 * Dit kan resulteren in één API request ipv 100 API requests als je API resource bv 100 entries heeft.
 */
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
