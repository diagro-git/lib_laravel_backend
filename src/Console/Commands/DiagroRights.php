<?php
namespace Diagro\Backend\Console\Commands;

use Diagro\API\API;
use Diagro\API\EndpointDefinition;
use Diagro\API\RequestMethod;
use Illuminate\Console\Command;

class DiagroRights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagro:rights';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Registreer de rechten en ken de rechten toe aan de gebruikte rollen.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $config = config('diagro');
        $url = env('CORE_API_URL') . '/_/rights';

        $definition = new EndpointDefinition($url, RequestMethod::PUT, '', $config['app_id']);
        $definition->setJsonKey(null)->noCache();

        API::withFail(fn($response) => $this->error($response->body()));
        API::backend($definition);
    }
}
