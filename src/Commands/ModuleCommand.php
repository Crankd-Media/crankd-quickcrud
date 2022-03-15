<?php

namespace Crankd\Quickcrud\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     * php artisan make:module Roles
     */
    protected $signature = 'make:module {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This is to make Module Folder Command ';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    private $stubs_path = __DIR__ . '/stubs/';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $moduleName = ucwords($this->argument('name'));
        $ModuleNameLow = strtolower($this->argument('name'));
        $moduleNameSingular = Str::singular($moduleName);
        $moduleNameSingularLow = strtolower($moduleNameSingular);

        $this->makeRoutes($moduleName);
        $this->makeModel($moduleName, $moduleNameSingular);
        $this->makeController($moduleName, $moduleNameSingular);
        $this->makeService($moduleName, $moduleNameSingular);
        $this->makeRequests($moduleName, $moduleNameSingular);
        $this->makeViews($moduleName, $ModuleNameLow, $moduleNameSingular);

        return $ModuleNameLow;
    }

    private function formatStubs($moduleName, $fileContents)
    {
        $ModuleNameLow = strtolower($moduleName);
        $moduleNameSingular = Str::singular($moduleName);
        $moduleNameSingularLow = strtolower($moduleNameSingular);

        $fileContents = str_replace("{{ ModuleName }}", $moduleName, $fileContents);
        $fileContents = str_replace("{{ ModuleNameLow }}", $ModuleNameLow, $fileContents);
        $fileContents = str_replace("{{ ModuleNameSingular }}", $moduleNameSingular, $fileContents);
        $fileContents = str_replace("{{ ModuleNameSingularLow }}", $moduleNameSingularLow, $fileContents);

        return $fileContents;
    }

    public function makeModel($moduleName, $moduleNameSingular)
    {

        // Model
        Artisan::call('make:model ' . $moduleNameSingular);
        $fileContents = file_get_contents($this->stubs_path . 'Model.stub');
        $fileContents = $this->formatStubs($moduleName, $fileContents);

        $filePath = base_path() . '/app/Models/' . $moduleNameSingular . '.php';
        if (File::exists($filePath)) {
            $written = File::put($filePath, $fileContents);
            $message = ($written) ? 'Created Models in ' . $filePath : "Something went wrong";
            $this->info($message);
        }
        $this->info($filePath);

        // Migration
        Artisan::call('make:migration', ['name' => 'create' . $moduleName . '_table']);
        $migration_name = str_replace('Created Migration: ', '', Artisan::output());
        $migration_name = preg_replace("/\r|\n/", "", $migration_name);

        $fileName = $migration_name . '.php';
        $filePath = base_path() . '/database/migrations/' . $fileName;
        $lines = file($filePath, FILE_IGNORE_NEW_LINES);

        $fileContents = file_get_contents($this->stubs_path . 'Migration.stub');
        $lines[17] = $fileContents;

        file_put_contents($filePath, implode("\n", $lines));
        $this->info("Created Migration " . $filePath);

        // Seeder
        Artisan::call('make:seeder ' . $moduleName . 'TableSeeder');
        $fileContents = file_get_contents($this->stubs_path . 'TableSeeder.stub');
        $fileContents = $this->formatStubs($moduleName, $fileContents);

        $fileName = $moduleName . 'TableSeeder.php';
        $filePath = base_path() . '/database/seeders/' . $fileName;

        if (!File::exists($filePath)) {
            $written = File::put($filePath, $fileContents);
            $message = ($written) ? 'Created Controller in ' . $filePath : "Something went wrong";
            $this->info($message);
        }
        $this->info("Created Seeder " . $filePath);

        // Factory
        Artisan::call('make:factory ' . $moduleNameSingular . 'Factory --model=' . $moduleNameSingular);
        $this->info("Created Factory " . $moduleNameSingular . 'Factory');

    }

    public function makeController($moduleName, $moduleNameSingular)
    {
        $controllers = [
            $moduleNameSingular . 'Controller',
        ];
        foreach ($controllers as $key => $controller) {

            Artisan::call('make:controller ' . $controller . ' --resource');

            $fileContents = file_get_contents($this->stubs_path . 'Controller.stub');
            $fileContents = $this->formatStubs($moduleName, $fileContents);

            $filePath = base_path() . '/app/http/Controllers/' . $controller . '.php';
            if (File::exists($filePath)) {
                $written = File::put($filePath, $fileContents);
                $message = ($written) ? 'Created Controller in ' . $filePath : "Something went wrong";
                $this->info($message);
            }
            $this->info("Created Controller " . $filePath);
        }
    }

    public function makeService($moduleName, $moduleNameSingular)
    {

        File::makeDirectory('app/Services', 0775, true, true);

        $services = [
            $moduleNameSingular . 'Service',
        ];
        foreach ($services as $key => $service) {

            $fileContents = file_get_contents($this->stubs_path . 'Service.stub');
            $fileContents = $this->formatStubs($moduleName, $fileContents);

            $filePath = base_path() . '/app/Services/' . $service . '.php';
            $this->info($filePath);
            if (!File::exists($filePath)) {
                $written = File::put($filePath, $fileContents);
                $message = ($written) ? 'Created Service in ' . $filePath : "Something went wrong";
                $this->info($message);
            }

        }
    }

    public function makeRequests($moduleName, $moduleNameSingular)
    {
        $requests = [
            'UpdateRequest',
            'StoreRequest',
        ];
        foreach ($requests as $key => $request) {

            $path = $moduleName . '/' . $moduleNameSingular . $request;
            $filePath = base_path() . '/app/http/Requests/' . $path . '.php';
            Artisan::call('make:request ' . $path);

            $fileContents = file_get_contents($this->stubs_path . 'Request.stub');
            $fileContents = str_replace("{{ RequestName }}", $request, $fileContents);
            $fileContents = $this->formatStubs($moduleName, $fileContents);

            if (File::exists($filePath)) {
                $written = File::put($filePath, $fileContents);
                $message = ($written) ? 'Created Request in ' . $filePath : "Something went wrong";
                $this->info($message);
            }

        }
    }

    public function makeViews($moduleName)
    {
        $destination = base_path() . '/resources/views/' . $moduleName;
        File::makeDirectory($destination, 0775, true, true);
        $files = [
            'index',
            'edit',
            'create',
            'show',
        ];

        foreach ($files as $key => $file) {
            $fileName = $file . '.blade.php';
            $filePath = $destination . '/' . $fileName;
            if (!File::exists($filePath)) {
                $fileContents = file_get_contents($this->stubs_path . '/views/' . $file . '.stub');
                $fileContents = $this->formatStubs($moduleName, $fileContents);

                $written = File::put($filePath, $fileContents);
                $message = ($written)
                ? 'Created Views in resources/views/' . $moduleName : "Something went wrong";
                $this->info($message);
            }
        }
    }

    public function makeRoutes($moduleName)
    {
        $fileContents = file_get_contents($this->stubs_path . '/Routes.stub');
        $fileContents = $this->formatStubs($moduleName, $fileContents);
        $filePath = base_path() . '/routes/web.php';
        $written = File::append($filePath, $fileContents);

        Artisan::call('route:cache');
        Artisan::call('route:clear');

        $message = ($written) ? 'Created Route in web.php' . $moduleName : "Something went wrong";
        $this->info($message);

    }

}