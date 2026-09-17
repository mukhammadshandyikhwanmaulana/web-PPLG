<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Buat role Spatie di database in-memory pengujian
        Role::findOrCreate(UserRole::Admin->value);
        Role::findOrCreate(UserRole::Guru->value);
    }

    public function test_halaman_login_dapat_diakses(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_admin_dapat_mengakses_dashboard_admin(): void
    {
        $admin = User::factory()->admin()->create();
        // Berikan role Spatie agar lolos middleware 'role:admin'
        $admin->assignRole(UserRole::Admin->value);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_guru_tidak_dapat_mengakses_dashboard_admin(): void
    {
        $guru = User::factory()->guru()->create();
        $guru->assignRole(UserRole::Guru->value);

        $response = $this->actingAs($guru)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_guru_dapat_mengakses_dashboard_guru(): void
    {
        $guru = User::factory()->guru()->create();
        $guru->assignRole(UserRole::Guru->value);

        $response = $this->actingAs($guru)->get('/guru/dashboard');
        $response->assertStatus(200);
    }
}