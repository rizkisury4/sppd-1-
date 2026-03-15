<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_name_is_required_when_updating_profile(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => '',
                'email' => 'valid@example.com',
            ]);

        $response->assertSessionHasErrors(['name'])->assertRedirect('/profile');

        $this->assertNotSame('valid@example.com', $user->fresh()->email);
    }

    public function test_email_must_be_unique_when_updating_profile(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create(['email' => 'existing@example.com']);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->patch('/profile', [
                'name' => 'User X',
                'email' => 'existing@example.com',
            ]);

        $response->assertSessionHasErrors(['email'])->assertRedirect('/profile');

        $this->assertNotSame('existing@example.com', $user->fresh()->email);
    }

    public function test_email_is_lowercased_automatically(): void
    {
        $user = User::factory()->create(['email' => 'Original@Example.com']);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'User Y',
                'email' => 'NEW.EMAIL@EXAMPLE.COM',
            ]);

        $response->assertSessionHasNoErrors()->assertRedirect('/profile');

        $this->assertSame('new.email@example.com', $user->fresh()->email);
    }

    public function test_guest_cannot_access_profile_page(): void
    {
        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    public function test_deleting_account_invalidates_session_and_logs_out(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response->assertRedirect('/');

        $this->assertGuest();
    }
}
