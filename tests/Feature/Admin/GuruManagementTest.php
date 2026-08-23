<?php

namespace Tests\Feature\Admin;

use App\Models\StaffMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GuruManagementTest extends TestCase
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

    public function test_admin_can_view_guru_list(): void
    {
        $this->actingAs($this->admin)->get(route('admin.guru.index'))->assertOk();
    }

    public function test_guru_cannot_view_guru_management(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');

        $this->actingAs($guru)->get(route('admin.guru.index'))->assertForbidden();
    }

    public function test_admin_can_create_guru_account(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.guru.store'), [
            'name' => 'Guru Baru',
            'email' => 'guru.baru@dev.local',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.guru.index'));

        $user = User::where('email', 'guru.baru@dev.local')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('guru'));
        $this->assertFalse($user->hasRole('admin'));
        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_duplicate_email_is_rejected(): void
    {
        $existing = User::factory()->create(['email' => 'dup@dev.local']);
        $existing->assignRole('guru');

        $response = $this->actingAs($this->admin)->post(route('admin.guru.store'), [
            'name' => 'Guru Lain',
            'email' => 'dup@dev.local',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_role_cannot_be_manipulated_via_request(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.guru.store'), [
            'name' => 'Guru Nakal',
            'email' => 'nakal@dev.local',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('admin.guru.index'));

        $user = User::where('email', 'nakal@dev.local')->first();
        $this->assertTrue($user->hasRole('guru'));
        $this->assertFalse($user->hasRole('admin'));
    }

    public function test_admin_can_update_guru_account(): void
    {
        $guru = User::factory()->create(['name' => 'Nama Lama']);
        $guru->assignRole('guru');

        $response = $this->actingAs($this->admin)->put(route('admin.guru.update', $guru), [
            'name' => 'Nama Baru',
            'email' => $guru->email,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.guru.index'));
        $this->assertEquals('Nama Baru', $guru->fresh()->name);
    }

    public function test_destroy_deactivates_instead_of_deleting(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');

        $this->actingAs($this->admin)->delete(route('admin.guru.destroy', $guru));

        $this->assertDatabaseHas('users', ['id' => $guru->id]);
        $this->assertFalse($guru->fresh()->is_active);
    }

    public function test_admin_account_cannot_be_accessed_via_guru_controller(): void
    {
        $otherAdmin = User::factory()->create();
        $otherAdmin->assignRole('admin');

        $this->actingAs($this->admin)->get(route('admin.guru.edit', $otherAdmin))->assertNotFound();
        $this->actingAs($this->admin)->delete(route('admin.guru.destroy', $otherAdmin))->assertNotFound();
    }

    public function test_deactivated_guru_cannot_login(): void
    {
        $guru = User::factory()->create(['is_active' => false]);
        $guru->assignRole('guru');
        $guru->password = 'password123';
        $guru->save();

        $response = $this->post('/guru/login', [
            'email' => $guru->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_admin_can_create_guru_with_full_profile(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.guru.store'), [
            'name' => 'Guru Lengkap',
            'email' => 'guru.lengkap@dev.local',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_active' => '1',
            'position' => 'Guru Produktif',
            'expertise' => 'Pemrograman Web',
            'sort_order' => '5',
            'staff_is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.guru.index'));

        $user = User::where('email', 'guru.lengkap@dev.local')->first();
        $this->assertNotNull($user);

        $staffMember = StaffMember::where('user_id', $user->id)->first();
        $this->assertNotNull($staffMember);
        $this->assertEquals('Guru Lengkap', $staffMember->name);
        $this->assertEquals('Guru Produktif', $staffMember->position);
        $this->assertEquals('Pemrograman Web', $staffMember->expertise);
        $this->assertEquals(5, $staffMember->sort_order);
        $this->assertTrue($staffMember->is_active);
    }

    public function test_update_password_empty_does_not_change_password(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        $originalHash = $guru->password;

        $this->actingAs($this->admin)->put(route('admin.guru.update', $guru), [
            'name' => $guru->name,
            'email' => $guru->email,
            'is_active' => '1',
        ]);

        $this->assertEquals($originalHash, $guru->fresh()->password);
    }

    public function test_admin_can_change_guru_password(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');

        $this->actingAs($this->admin)->put(route('admin.guru.update', $guru), [
            'name' => $guru->name,
            'email' => $guru->email,
            'is_active' => '1',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $this->assertTrue(Hash::check('passwordbaru123', $guru->fresh()->password));
    }

    public function test_photo_upload_creates_media_and_links_to_staff_member(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->admin)->post(route('admin.guru.store'), [
            'name' => 'Guru Berfoto',
            'email' => 'guru.berfoto@dev.local',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'photo' => UploadedFile::fake()->image('foto.jpg'),
        ]);

        $response->assertRedirect(route('admin.guru.index'));

        $user = User::where('email', 'guru.berfoto@dev.local')->first();
        $staffMember = StaffMember::where('user_id', $user->id)->first();

        $this->assertNotNull($staffMember->photo_media_id);
        Storage::disk('public')->assertExists($staffMember->photo->file_path);
    }

    public function test_staff_profile_status_independent_from_account_status(): void
    {
        $this->actingAs($this->admin)->post(route('admin.guru.store'), [
            'name' => 'Guru Status Beda',
            'email' => 'guru.statusbeda@dev.local',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_active' => '1',
            'staff_is_active' => '0',
        ]);

        $user = User::where('email', 'guru.statusbeda@dev.local')->first();
        $staffMember = StaffMember::where('user_id', $user->id)->first();

        $this->assertTrue($user->is_active);
        $this->assertFalse($staffMember->is_active);
    }

    public function test_destroy_deactivates_both_account_and_profile(): void
    {
        $this->actingAs($this->admin)->post(route('admin.guru.store'), [
            'name' => 'Guru Akan Nonaktif',
            'email' => 'guru.nonaktif@dev.local',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'guru.nonaktif@dev.local')->first();

        $this->actingAs($this->admin)->delete(route('admin.guru.destroy', $user));

        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertFalse($user->fresh()->is_active);
        $this->assertFalse($user->fresh()->staffMember->is_active);
    }

    public function test_editing_legacy_guru_without_staff_member_creates_one(): void
    {
        $guru = User::factory()->create();
        $guru->assignRole('guru');
        $this->assertNull($guru->staffMember);

        $this->actingAs($this->admin)->put(route('admin.guru.update', $guru), [
            'name' => $guru->name,
            'email' => $guru->email,
            'is_active' => '1',
            'position' => 'Guru Baru Ditambahkan',
        ]);

        $staffMember = StaffMember::where('user_id', $guru->id)->first();
        $this->assertNotNull($staffMember);
        $this->assertEquals('Guru Baru Ditambahkan', $staffMember->position);
    }
}