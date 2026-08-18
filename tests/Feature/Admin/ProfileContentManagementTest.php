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
            'vision_mission_content' => 'Visi misi baru.',
            'about_excerpt' => 'Ringkasan baru.',
            'meta_title' => 'Judul SEO',
            'meta_description' => 'Deskripsi SEO.',
        ]);

        $response->assertRedirect(route('admin.profil.edit'));

        $this->assertDatabaseHas('profile_contents', [
            'history_content' => 'Sejarah baru.',
            'vision_mission_content' => 'Visi misi baru.',
            'about_excerpt' => 'Ringkasan baru.',
            'meta_title' => 'Judul SEO',
            'meta_description' => 'Deskripsi SEO.',
        ]);
    }

    public function test_updated_by_is_set_from_authenticated_admin(): void
    {
        $this->actingAs($this->admin)->put(route('admin.profil.update'), [
            'meta_title' => 'Judul',
        ]);

        $profile = ProfileContent::first();
        $this->assertEquals($this->admin->id, $profile->updated_by);
    }

    public function test_meta_title_over_60_characters_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.profil.update'), [
            'meta_title' => str_repeat('a', 61),
        ]);

        $response->assertSessionHasErrors('meta_title');
    }

    public function test_meta_description_over_160_characters_is_rejected(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.profil.update'), [
            'meta_description' => str_repeat('a', 161),
        ]);

        $response->assertSessionHasErrors('meta_description');
    }
}