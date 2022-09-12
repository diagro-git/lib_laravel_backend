<?php
namespace Diagro\Backend\Console\Commands;

use Diagro\Token\BackendApplicationToken;
use Illuminate\Console\Command;

class BackendTokenGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'diagro:backend-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a backend token and store it in the .env file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $app_id = env('DIAGRO_APP_ID');
        if(empty($app_id)) {
            $this->error("No env variable DIAGRO_APP_ID found!");
            return 0;
        }

        $app_name = env('DIAGRO_APP_NAME');
        if(empty($app_name)) {
            $this->error("No env variable DIAGRO_APP_NAME found!");
            return 0;
        }

        $path = base_path('.env');
        if(! file_exists($path)) {
            $this->error('.env file not found!');
            return 0;
        }

        //only auth.domain.ext are allowed to generate tokens.
        //little hack for generating backend tokens
        config()->set('app.url', 'https://auth.' . explode('.', config('app.url'), 2)[1]);

        $bat = new BackendApplicationToken($app_id, $app_name);
        $bat = $bat->token();
        $contents = file_get_contents($path);
        if(str_contains($contents, 'DIAGRO_BACKEND_TOKEN=')) {
            $old_bat = env('DIAGRO_BACKEND_TOKEN');
            file_put_contents($path, str_replace("DIAGRO_BACKEND_TOKEN=" . $old_bat, "DIAGRO_BACKEND_TOKEN=" . $bat, $contents));
        } else {
            file_put_contents($path, "DIAGRO_BACKEND_TOKEN=" . $bat . PHP_EOL, FILE_APPEND);
        }

        $this->info("DIAGRO_BACKEND_TOKEN env variable is added to .env file!");

        return 1;
    }
}
