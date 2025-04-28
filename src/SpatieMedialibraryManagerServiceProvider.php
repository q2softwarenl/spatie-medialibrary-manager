<?php

namespace Q2softwarenl\SpatieMedialibraryManager;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Q2softwarenl\SpatieMedialibraryManager\Livewire\Manager;

class SpatieMedialibraryManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureViews();
        $this->configureTranslations();
        $this->registerConfig();
        $this->registerLivewireComponents();
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    protected function configureViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'spatie-medialibrary-manager');
    }

    protected function configureTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'smm');

        $this->publishes(
            [__DIR__.'/../stubs/lang/mediaCollections.php' => $this->app->langPath('mediaCollections.php')],
            'lang'
        );
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/media-library-manager.php', 'media-library-manager');
        
        $this->publishes(
            [__DIR__.'/config/media-library-manager.php' => config_path('media-library-manager.php')],
            'config'
        );
    }

    protected function registerLivewireComponents(): void
    {
        Livewire::component('spatie-medialibrary-manager', Manager::class);
    }

}