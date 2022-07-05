<?php
namespace Diagro\Backend\Console\Commands;

use Illuminate\Foundation\Console\RequestMakeCommand;
use Illuminate\Foundation\Console\ResourceMakeCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'diagro:resource')]
class DiagroResource extends ResourceMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'diagro:resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Maak een resource voor Diagro platform';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->collection() ?
            __DIR__ . '/../../../stubs/resource-collection.stub' :
            __DIR__ . '/../../../stubs/resource.stub';
    }
}
