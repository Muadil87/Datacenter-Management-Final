<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Resource;
use App\Models\ResourceCategory;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ResourceCrudTest extends TestCase
{
    use DatabaseTransactions;

    public function test_admin_can_create_update_and_delete_resource(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $category = ResourceCategory::create([
            'name' => 'Serveur',
            'description' => 'Test category',
        ]);

        $this->actingAs($admin);

        $createResponse = $this->post('/resources', [
            'name' => 'Test Resource',
            'category_id' => $category->id,
            'state' => 'available',
            'cpu_cores' => 4,
            'ram_gb' => 16,
            'storage_gb' => 256,
            'storage_type' => 'SSD',
            'bandwidth_mbps' => 100,
        ]);

        $createResponse->assertStatus(302);

        $resource = Resource::where('name', 'Test Resource')->first();
        $this->assertNotNull($resource);

        $updateResponse = $this->patch('/resources/' . $resource->id, [
            'name' => 'Test Resource Updated',
            'state' => 'maintenance',
            'cpu_cores' => 8,
            'ram_gb' => 32,
            'storage_gb' => 512,
            'storage_type' => 'SSD',
            'bandwidth_mbps' => 200,
        ]);

        $updateResponse->assertStatus(302);
        $this->assertDatabaseHas('resources', [
            'id' => $resource->id,
            'name' => 'Test Resource Updated',
            'state' => 'maintenance',
        ]);

        $deleteResponse = $this->delete('/resources/' . $resource->id);
        $deleteResponse->assertStatus(302);
        $this->assertDatabaseMissing('resources', [
            'id' => $resource->id,
        ]);
    }
}
