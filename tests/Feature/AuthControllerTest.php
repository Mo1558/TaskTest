<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_the_register_form()
    {
        $response = $this->get(route('register.form'));
        $response->assertStatus(200)->assertViewIs('auth.register');
    }

    /** @test */
    public function it_allows_user_registration_and_assigns_role()
    {
        // Arrange: Create roles
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Manager']);
        Role::create(['name' => 'User']);

        // Act: Submit registration form
        $response = $this->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'Admin',
        ]);

        // Assert: Verify user and role assignment
        $response->assertRedirect(route('home'));

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertTrue($user->hasRole('Admin'));

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_displays_the_login_form()
    {
        $response = $this->get(route('login.form'));
        $response->assertStatus(200)->assertViewIs('auth.login');
    }

    /** @test */
    public function it_allows_user_login()
    {
        // Arrange: Create a user
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Act: Attempt login
        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        // Assert: Verify successful login
        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_prevents_login_with_invalid_credentials()
    {
        // Arrange: Create a user
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Act: Attempt login with invalid credentials
        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        // Assert: Verify failed login
        $response->assertStatus(302)
            ->assertSessionHasErrors(['email' => 'The provided credentials do not match our records.']);
        $this->assertGuest();
    }

    /** @test */
    public function it_allows_user_logout()
    {
        // Arrange: Log in a user
        $user = User::factory()->create();
        $this->actingAs($user);

        // Act: Log out
        $response = $this->post(route('logout'));

        // Assert: Verify successful logout
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
