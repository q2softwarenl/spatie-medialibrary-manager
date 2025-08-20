<?php

namespace Q2softwarenl\SpatieMedialibraryManager\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Concerns\WithWorkbench;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use WithWorkbench;
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        
        //
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
