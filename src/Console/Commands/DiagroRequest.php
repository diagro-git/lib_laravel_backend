<?php
namespace Diagro\Backend\Console\Commands;

use Illuminate\Foundation\Console\RequestMakeCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'diagro:request')]
class DiagroRequest extends RequestMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'diagro:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Maak een request voor Diagro platform';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/request.stub';
    }
}
