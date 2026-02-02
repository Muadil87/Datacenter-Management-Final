<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NotificationApiTest extends TestCase
{
    use DatabaseTransactions;

    public function test_notifications_api_returns_user_notifications(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Test Notification',
            'message' => 'Test message',
            'type' => 'general',
            'is_read' => false,
        ]);

        $response = $this->actingAs($user)->get('/api/notifications?limit=10');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'title' => 'Test Notification',
            'message' => 'Test message',
        ]);
    }
}
