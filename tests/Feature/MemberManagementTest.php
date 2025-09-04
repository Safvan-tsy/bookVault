<?php

namespace Tests\Feature;

use App\Models\User;
use App\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MemberManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_members_index()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $member = User::factory()->create(['role' => UserRole::MEMBER]);

        $response = $this->actingAs($admin)->get(route('members.index'));

        $response->assertStatus(200);
        $response->assertSee($member->name);
        $response->assertSee($member->email);
    }

    public function test_member_cannot_access_member_management()
    {
        $member = User::factory()->create(['role' => UserRole::MEMBER]);

        $response = $this->actingAs($member)->get(route('members.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_create_new_member()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $memberData = [
            'name' => 'New Member',
            'email' => 'newmember@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'member',
        ];

        $response = $this->actingAs($admin)->post(route('members.store'), $memberData);

        $response->assertRedirect(route('members.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'New Member',
            'email' => 'newmember@example.com',
            'role' => 'member',
        ]);
    }

    public function test_admin_can_update_member()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $member = User::factory()->create(['role' => UserRole::MEMBER]);

        $updateData = [
            'name' => 'Updated Name',
            'email' => $member->email,
            'role' => 'admin',
        ];

        $response = $this->actingAs($admin)->put(route('members.update', $member), $updateData);

        $response->assertRedirect(route('members.index'));
        $this->assertDatabaseHas('users', [
            'id' => $member->id,
            'name' => 'Updated Name',
            'role' => 'admin',
        ]);
    }

    public function test_admin_can_toggle_member_role()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $member = User::factory()->create(['role' => UserRole::MEMBER]);

        $response = $this->actingAs($admin)->patch(route('members.toggle-role', $member));

        $response->assertRedirect();
        $member->refresh();
        $this->assertEquals(UserRole::ADMIN, $member->role);
    }

    public function test_admin_cannot_change_own_role()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($admin)->patch(route('members.toggle-role', $admin));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $admin->refresh();
        $this->assertEquals(UserRole::ADMIN, $admin->role);
    }

    public function test_admin_can_delete_member_without_active_borrows()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $member = User::factory()->create(['role' => UserRole::MEMBER]);

        $response = $this->actingAs($admin)->delete(route('members.destroy', $member));

        $response->assertRedirect(route('members.index'));
        $this->assertDatabaseMissing('users', ['id' => $member->id]);
    }

    public function test_admin_cannot_delete_self()
    {
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);

        $response = $this->actingAs($admin)->delete(route('members.destroy', $admin));

        $response->assertRedirect(route('members.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
