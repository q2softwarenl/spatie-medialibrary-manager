<?php

namespace Q2softwarenl\SpatieMedialibraryManager\Tests\Feature;

use Livewire\Livewire;
use Q2softwarenl\SpatieMedialibraryManager\Livewire\Manager;
use Q2softwarenl\SpatieMedialibraryManager\Tests\TestCase;
use Workbench\App\Models\User;

class ExampleTest extends TestCase
{
    public $model;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->model = User::factory()->create();
    }

    public function test_renders_successfully()
    {
        Livewire::test(Manager::class, ['model' => $this->model])
            ->assertStatus(200);
    }

    

}
