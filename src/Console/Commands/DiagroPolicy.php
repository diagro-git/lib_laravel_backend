<?php
namespace Diagro\Backend\Console\Commands;

use Illuminate\Foundation\Console\PolicyMakeCommand;

class DiagroPolicy extends PolicyMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagro:policy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Maak een policy voor Diagro platform';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/policy.stub';
    }
}
