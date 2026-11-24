<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_toggle_user_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $this->actingAs($admin)
            ->get('/admin/nguoidung')
            ->assertOk()
            ->assertSee($user->email)
            ->assertSee('Hoạt động');

        $this->actingAs($admin)
            ->patch("/admin/nguoidung/{$user->id}/trang-thai", ['is_active' => 0])
            ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => false,
        ]);
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => 'password',
            'is_active' => false,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_must_confirm_admin_password_before_resetting_user_password(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'admin-password',
            'is_active' => true,
        ]);
        $user = User::factory()->create([
            'role' => 'user',
            'password' => 'old-password',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post("/admin/nguoidung/sua/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'user',
            'is_active' => 1,
            'doimatkhau' => 1,
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));

        $this->actingAs($admin)->post("/admin/nguoidung/sua/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'user',
            'is_active' => 1,
            'doimatkhau' => 1,
            'current_password' => 'admin-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
            ->assertRedirect('/admin/nguoidung');

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_reset_password_requires_new_password_and_confirmation(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'password' => 'admin-password',
            'is_active' => true,
        ]);
        $user = User::factory()->create([
            'role' => 'user',
            'password' => 'old-password',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->post("/admin/nguoidung/sua/{$user->id}", [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'user',
            'is_active' => 1,
            'doimatkhau' => 1,
            'current_password' => 'admin-password',
        ])
            ->assertSessionHasErrors(['password']);

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }
}
