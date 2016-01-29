<?php namespace Jimtendo\FileManager;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->handleConfigs();
        $this->handleViews();
        // $this->handleTranslations();
        // $this->handleRoutes();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {

        return [];
    }

    private function handleConfigs() {

        $this->publishes([__DIR__ . '/../config/filemanager.php' => config_path('filemanager.php')], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../config/filemanager.php', 'filemanager');
    }

    private function handleTranslations() {

        $this->loadTranslationsFrom('jazzycrud', __DIR__.'/../lang');
    }

    private function handleViews()
    {
        $this->loadViewsFrom(__DIR__.'/../views', 'filemanager');

        $this->publishes([__DIR__.'/../views' => base_path('resources/views/vendor/jimtendo/filemanager')]);
    }
}
