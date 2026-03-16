<?php

namespace Q2softwarenl\SpatieMedialibraryManager\Tests\Feature;

use Livewire\Livewire;
use Q2softwarenl\SpatieMedialibraryManager\Livewire\Manager;
use Q2softwarenl\SpatieMedialibraryManager\Tests\TestCase;
use Workbench\App\Models\User;

class ManagerTest extends TestCase
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

    public function test_model_must_implement_interface()
    {

    }

    public function test_uploads_to_given_model()
    {

    }

    public function test_uploads_to_selected_collection()
    {

    }

    public function test_renaming_file_updates_human_filename_but_not_system_file_path()
    {

    }

    public function test_set_allowed_mime_types_validation()
    {

    }

    public function test_single_file_collections()
    {

    }

    public function test_cannot_upload_multiple_files_to_single_file_collections()
    {

    }

    public function test_move_file_to_other_collection()
    {

    }

    public function test_can_edit_property_cannot_override_if_policy_doesnt_allow_it()
    {

    }

    public function test_can_edit_property_can_block_policy()
    {

    }

    public function test_can_set_max_file_uplaod_size_per_file()
    {

    }

    public function test_can_set_max_file_uplaod_size_for_collection()
    {

    }

    public function test_can_delete_property_cannot_override_if_policy_doesnt_allow_it()
    {

    }

    public function test_can_delete_property_can_block_policy()
    {

    }

    public function test_can_move_property_cannot_override_if_policy_doesnt_allow_it()
    {

    }

    public function test_can_move_property_can_block_policy()
    {

    }

    public function test_can_upload_property_cannot_override_if_policy_doesnt_allow_it()
    {
        
    }

    public function test_can_upload_property_can_block_policy()
    {

    }

    public function test_can_download_property_cannot_override_if_policy_doesnt_allow_it()
    {

    }

    public function test_can_download_property_can_block_policy()
    {

    }

    public function test_can_download_all_property_cannot_override_if_policy_doesnt_allow_it()
    {

    }

    public function test_can_download_all_property_can_block_policy()
    {

    }

    public function test_cannot_overview_collections_if_model_has_one_collection()
    {

    }

    public function test_cannot_move_to_collection_if_one_collection_is_filtered_from_multiple_collections()
    {

    }

    public function test_filter_collection_from_multiple_collections()
    {

    }

    public function test_cannot_overview_collections_if_one_collection_is_filtered_from_multiple_collections()
    {

    }

    public function test_can_overview_collections_if_multiple_collections()
    {

    }

    public function test_config_update_default_validation_mimes_has_effect()
    {

    }

}
