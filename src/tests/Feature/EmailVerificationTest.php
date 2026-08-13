<?php

namespace Tests\Feature;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    // 会員登録後、認証メールが送信される
    public function test_registration_sends_verification_email()
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'email@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(302);

        $user = User::where('email', 'email@example.com')->first();

        Notification::assertSentTo(
            $user,
            VerifyEmail::class
        );
    }

    // 「認証はこちらから」を押すとメール認証サイトに遷移する
    public function test_user_can_access_email_verification_site()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get('/email/verify-confirm');

        $response->assertStatus(200);
        $response->assertViewIs('auth.verify');
    }

    // メール認証完了後、勤怠登録画面に遷移する
    public function test_user_can_verify_email()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $user->refresh();

        $this->assertNotNull($user->email_verified_at);

        $response->assertRedirect('/attendance');
    }
}