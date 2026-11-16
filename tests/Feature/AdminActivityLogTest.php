<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admin_can_view_activity_logs(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user)->get('/admin/lich-su')
            ->assertRedirect();

        $this->actingAs($admin)->get('/admin/lich-su')
            ->assertOk()
            ->assertSee('Lịch sử hoạt động');
    }

    public function test_authenticated_requests_are_recorded(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get('/admin/lich-su');

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'method' => 'GET',
            'route_name' => 'admin.lichsu.danhsach',
            'action' => 'view',
        ]);
    }

    public function test_admin_can_delete_an_activity_log(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $log = ActivityLog::record($admin, 'test', 'Bản ghi kiểm thử', 'test', 'GET', 'http://localhost/test');

        $this->actingAs($admin)->delete('/admin/lich-su/'.$log->id)
            ->assertRedirect();

        $this->assertDatabaseMissing('activity_logs', ['id' => $log->id]);
    }
}
