<?php
namespace Diagro\Backend\Console\Commands;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand;

class DiagroMigration extends MigrateMakeCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'diagro:migration {name : The name of the migration}
        {--create= : The table to be created}
        {--table= : The table to migrate}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration}';

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
