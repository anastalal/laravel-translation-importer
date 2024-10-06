<?php
namespace Anastalal\LaravelTranslationImporter;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Anastalal\LaravelTranslationImporter\Console\Commands\ListMissingTranslationKeys;
use Anastalal\LaravelTranslationImporter\Console\Commands\SynchroniseTranslationsCommand;
use Anastalal\LaravelTranslationImporter\Console\Commands\SynchroniseMissingTranslationKeys;


class TranslationImporterServiceProvider extends ServiceProvider{

    public function boot()
    {
       
        $this->publishConfiguration();

        $this->loadMigrations();

        $this->loadTranslations();

        $this->registerHelpers();
    }
    public function register()
    {
        $this->mergeConfiguration();

        $this->registerCommands();

        $this->registerContainerBindings();
    }



     /**
     * Publish package configuration.
     *
     * @return void
     */
    private function publishConfiguration()
    {
        $this->publishes([
            __DIR__.'/../config/translation.php' => config_path('translation.php'),
        ], 'config');
    }

    /**
     * Merge package configuration.
     *
     * @return void
     */
    private function mergeConfiguration()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/translation.php', 'translation');
    }


      /**
     * Load package migrations.
     *
     * @return void
     */
    private function loadMigrations()
    {
        if (config('translation.driver') !== 'database') {
            return;
        }

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Load package translations.
     *
     * @return void
     */
    private function loadTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'translation');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/translation'),
        ]);
    }


     /**
     * Register package commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                // AddLanguageCommand::class,
                // AddTranslationKeyCommand::class,
                // ListLanguagesCommand::class,
                // ListMissingTranslationKeys::class,
                ListMissingTranslationKeys::class,
                SynchroniseTranslationsCommand::class,
                SynchroniseMissingTranslationKeys::class,
                // ::class,
            ]);
        }
    }

      /**
     * Register package helper functions.
     *
     * @return void
     */
    private function registerHelpers()
    {
        require __DIR__.'/../resources/helpers.php';
    }

    /**
     * Register package bindings in the container.
     *
     * @return void
     */
    private function registerContainerBindings()
    {
        $this->app->singleton(Scanner::class, function () {
            $config = $this->app['config']['translation'];

            return new Scanner(new Filesystem(), $config['scan_paths'], $config['translation_methods']);
        });

        $this->app->singleton(Translation::class, function ($app) {
            return (new TranslationManager($app, $app['config']['translation'], $app->make(Scanner::class)))->resolve();
        });
    }
}