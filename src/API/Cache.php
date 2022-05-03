<?php
namespace Diagro\Backend\API;

use Diagro\API\ApiEndpoints;
use Diagro\API\EndpointDefinition;
use Diagro\API\RequestMethod;

class Cache
{

    use ApiEndpoints;


    public function fetch(): EndpointDefinition
    {
        $endpoint = new EndpointDefinition($this->url('/'), RequestMethod::GET, $this->getToken(), $this->getAppId());
        $endpoint->setJsonKey(null);
        return $endpoint;
    }


    public function store(array $data, array $usedResources): EndpointDefinition
    {
        $endpoint = new EndpointDefinition($this->url('/'), RequestMethod::POST, $this->getToken(), $this->getAppId());
        $endpoint->setData([
            'data' => $data,
            'usedResources' => $usedResources
        ]);
        return $endpoint;
    }


    public function delete(array $resources): EndpointDefinition
    {
        $endpoint = new EndpointDefinition($this->url('/'), RequestMethod::DELETE, $this->getToken(), $this->getAppId());
        $endpoint->setData([
            'resources' => $resources
        ]);
        return $endpoint;
    }


    protected function url(string $path): string
    {
        return env('DIAGRO_SERVICE_CACHE_URI', '');
    }
}