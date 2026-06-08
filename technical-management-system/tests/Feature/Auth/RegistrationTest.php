<?php

namespace Tests\Feature\Auth;

use App\Mail\RegistrationSuccessfulMail;
use App\Models\User;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));
        $response->assertSessionHas('status', 'Registration successful. Your account is pending admin approval.');

        Mail::assertSent(RegistrationSuccessfulMail::class, function (RegistrationSuccessfulMail $mail): bool {
            return $mail->hasTo('test@example.com');
        });
    }

    public function test_registration_clears_existing_session_state(): void
    {
        Mail::fake();

        $existingUser = User::factory()->create();

        $response = $this->actingAs($existingUser)
            ->withoutMiddleware(RedirectIfAuthenticated::class)
            ->post('/register', [
                'name' => 'New User',
                'email' => 'new@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));
        $response->assertSessionHas('status', 'Registration successful. Your account is pending admin approval.');
    }
}
