<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'guru', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    }

    protected function makeGuru(array $overrides = []): User
    {
        $user = User::factory()->create(array_merge([
            'password' => 'password-guru',
            'is_active' => true,
        ], $overrides));
        $user->assignRole('guru');

        return $user;
    }

    protected function makeAdmin(array $overrides = []): User
    {
        $user = User::factory()->create(array_merge([
            'password' => 'password-admin',
            'is_active' => true,
        ], $overrides));
        $user->assignRole('admin');

        return $user;
    }

    // ==================== PUBLIC ====================

    public function test_guest_can_open_public_route(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_guest_cannot_access_guru_area(): void
    {
        $this->get('/guru')->assertRedirect(route('guru.login'));
    }

    public function test_guest_cannot_access_admin_area(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }

    // ==================== GURU ====================

    public function test_guru_can_login_via_guru_login(): void
    {
        $guru = $this->makeGuru(['email' => 'guru1@dev.local']);

        $response = $this->post('/guru/login', [
            'email' => 'guru1@dev.local',
            'password' => 'password-guru',
        ]);

        $response->assertRedirect(route('guru.dashboard'));
        $this->assertAuthenticatedAs($guru);
    }

    public function test_guru_can_access_guru_area_after_login(): void
    {
        $guru = $this->makeGuru(['email' => 'guru2@dev.local']);
        $this->actingAs($guru);

        $this->get('/guru')->assertOk();
    }

    public function test_guru_cannot_access_admin_area(): void
    {
        $guru = $this->makeGuru(['email' => 'guru3@dev.local']);
        $this->actingAs($guru);

        $this->get('/admin')->assertForbidden();
    }

    public function test_guru_can_logout(): void
    {
        $guru = $this->makeGuru(['email' => 'guru4@dev.local']);
        $this->actingAs($guru);

        $response = $this->post('/guru/logout');

        $response->assertRedirect(route('guru.login'));
        $this->assertGuest();
    }

    // ==================== ADMIN ====================

    public function test_admin_can_login_via_admin_login(): void
    {
        $admin = $this->makeAdmin(['email' => 'admin1@dev.local']);

        $response = $this->post('/admin/login', [
            'email' => 'admin1@dev.local',
            'password' => 'password-admin',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_can_access_admin_area_after_login(): void
    {
        $admin = $this->makeAdmin(['email' => 'admin2@dev.local']);
        $this->actingAs($admin);

        $this->get('/admin')->assertOk();
    }

    public function test_admin_can_logout(): void
    {
        $admin = $this->makeAdmin(['email' => 'admin3@dev.local']);
        $this->actingAs($admin);

        $response = $this->post('/admin/logout');

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    // ==================== ROLE MISMATCH ====================

    public function test_admin_account_rejected_via_guru_login(): void
    {
        $this->makeAdmin(['email' => 'admin4@dev.local']);

        $response = $this->post('/guru/login', [
            'email' => 'admin4@dev.local',
            'password' => 'password-admin',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_guru_account_rejected_via_admin_login(): void
    {
        $this->makeGuru(['email' => 'guru5@dev.local']);

        $response = $this->post('/admin/login', [
            'email' => 'guru5@dev.local',
            'password' => 'password-guru',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // ==================== SECURITY ====================

    public function test_password_is_hashed_not_plaintext(): void
    {
        $guru = $this->makeGuru(['email' => 'guru6@dev.local']);

        $this->assertNotEquals('password-guru', $guru->password);
        $this->assertTrue(Hash::check('password-guru', $guru->password));
    }

    public function test_session_regenerates_after_login(): void
    {
        $this->makeGuru(['email' => 'guru7@dev.local']);

        $this->get('/guru/login');
        $oldSessionId = session()->getId();

        $this->post('/guru/login', [
            'email' => 'guru7@dev.local',
            'password' => 'password-guru',
        ]);

        $this->assertNotEquals($oldSessionId, session()->getId());
    }

    public function test_inactive_user_cannot_login(): void
    {
        $this->makeGuru(['email' => 'guru8@dev.local', 'is_active' => false]);

        $response = $this->post('/guru/login', [
            'email' => 'guru8@dev.local',
            'password' => 'password-guru',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_is_rate_limited(): void
    {
        $this->makeGuru(['email' => 'guru9@dev.local']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/guru/login', [
                'email' => 'guru9@dev.local',
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->post('/guru/login', [
            'email' => 'guru9@dev.local',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
    }
}