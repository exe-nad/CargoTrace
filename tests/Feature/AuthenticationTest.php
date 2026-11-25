<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'operator@cargotrace.com',
            'password' => Hash::make('password123'),
            'role' => 'operator',
        ]);

        $response = $this->post('/login', [
            'email' => 'operator@cargotrace.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'admin@cargotrace.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@cargotrace.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_api_login_returns_sanctum_token(): void
    {
        User::factory()->create([
            'email' => 'api_user@cargotrace.com',
            'password' => Hash::make('secret123'),
            'role' => 'customer',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'api_user@cargotrace.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                    'token_type',
                ],
            ]);
    }

    public function test_user_can_register_with_specified_role(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Nadya Salsabila Zahra',
            'role'                  => 'operator',
            'email'                 => 'nadya.operator@cargotrace.com',
            'company_name'          => 'Maritime Logistics Int',
            'phone'                 => '+62 812-9988-7766',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'name'         => 'Nadya Salsabila Zahra',
            'email'        => 'nadya.operator@cargotrace.com',
            'role'         => 'operator',
            'company_name' => 'Maritime Logistics Int',
            'phone'        => '+62 812-9988-7766',
        ]);
    }

    public function test_authenticated_user_can_view_profile_page(): void
    {
        $user = User::factory()->create([
            'name'  => 'Captain Haddock',
            'role'  => 'admin',
            'email' => 'haddock@cargotrace.com',
        ]);

        $response = $this->actingAs($user)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee('Captain Haddock');
        $response->assertSee('Administrator Sistem');
        $response->assertSee('haddock@cargotrace.com');
    }

    public function test_authenticated_user_can_update_profile_biodata(): void
    {
        $user = User::factory()->create([
            'name'         => 'Original Name',
            'email'        => 'user@cargotrace.com',
            'company_name' => 'Old Logistics',
            'phone'        => '0812345678',
        ]);

        $response = $this->actingAs($user)->put('/profile', [
            'name'         => 'Updated Name',
            'email'        => 'updated@cargotrace.com',
            'company_name' => 'New Logistics Corp',
            'phone'        => '0898765432',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id'           => $user->id,
            'name'         => 'Updated Name',
            'email'        => 'updated@cargotrace.com',
            'company_name' => 'New Logistics Corp',
            'phone'        => '0898765432',
        ]);
    }

    public function test_authenticated_user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password-123'),
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password'      => 'old-password-123',
            'password'              => 'new-strong-password',
            'password_confirmation' => 'new-strong-password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('new-strong-password', $user->password));
    }
}
