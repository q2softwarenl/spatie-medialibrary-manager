<?php

namespace Q2softwarenl\SpatieMedialibraryManager\Tests;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        
        // ..
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app)
    {
        return [
            \Q2softwarenl\SpatieMedialibraryManager\SpatieMedialibraryManagerServiceProvider::class,
            \Livewire\LivewireServiceProvider::class
        ];
    }
}
