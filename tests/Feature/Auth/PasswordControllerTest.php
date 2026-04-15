<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Should return validation error when current password is incorrect
     */
    public function test_should_return_validation_error_when_current_password_is_incorrect()
    {
        $user = User::factory()->create(['password' => Hash::make('current-secret')]);

        $response = $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    /**
     * Should return validation error when new password and confirmation do not match
     */
    public function test_should_return_validation_error_when_new_password_confirmation_mismatch()
    {
        $user = User::factory()->create(['password' => Hash::make('current-secret')]);

        $response = $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'current-secret',
            'password' => 'newpassword',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Should successfully update password when inputs are valid
     */
    public function test_should_successfully_update_password_when_inputs_are_valid()
    {
        $user = User::factory()->create(['password' => Hash::make('current-secret')]);

        $response = $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'current-secret',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    /**
     * Should require authentication to update password
     */
    public function test_should_require_authentication_to_update_password()
    {
        $response = $this->put(route('password.update'), [
            'current_password' => 'x',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertRedirect(route('login'));
    }

    /**
     * Should enforce password minimum length
     */
    public function test_should_enforce_password_minimum_length()
    {
        $user = User::factory()->create(['password' => Hash::make('current-secret')]);

        $response = $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'current-secret',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
