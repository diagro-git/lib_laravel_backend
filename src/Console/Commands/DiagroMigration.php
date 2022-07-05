<?php
namespace Diagro\Backend\Console\Commands;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'diagro:migration')]
class DiagroMigration extends MigrateMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'diagro:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Maak een migration voor Diagro platform';

    /**
     * Create a new migration install command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $creator = app('diagro.creator');
        $composer = app('composer');

        parent::__construct($creator, $composer);
    }


}
