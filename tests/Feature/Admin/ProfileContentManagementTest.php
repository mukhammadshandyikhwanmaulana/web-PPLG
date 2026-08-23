<?php

namespace Tests\Feature\Admin;

use App\Models\ProfileContent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileContentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_guest_is_redirected_to_admin_login(): void
    {
        $this->get('/admin/profil')->assertRedirect(route('admin.login'));
    }

    public function test_guru_cannot_access_profile_content_management(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');

        $this->actingAs($guru)->get('/admin/profil')->assertForbidden();
    }

    public function test_admin_can_open_edit_form(): void
    {
        $this->actingAs($this->admin)->get('/admin/profil')->assertOk();
    }

    public function test_admin_can_update_profile_content(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.profil.update'), [
            'history_content' => 'Sejarah baru.',
            'vision_content' => 'Visi baru.',
            'mission_content' => 'Misi baru.',
            'about_excerpt' => 'Ringkasan baru.',
        ]);

        $response->assertRedirect(route('admin.profil.edit'));

        $this->assertDatabaseHas('profile_contents', [
            'history_content' => 'Sejarah baru.',
            'vision_content' => 'Visi baru.',
            'mission_content' => 'Misi baru.',
            'about_excerpt' => 'Ringkasan baru.',
        ]);
    }

    public function test_updated_by_is_set_from_authenticated_admin(): void
    {
        $this->actingAs($this->admin)->put(route('admin.profil.update'), [
            'history_content' => 'Pembaruan Sejarah.',
        ]);

        $profile = ProfileContent::first();
        $this->assertEquals($this->admin->id, $profile->updated_by);
    }
}