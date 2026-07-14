<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

it('can register and get authenticated', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Demo User',
        'email' => 'demo@example.com',
        'phone' => '+628123456789',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'terms' => 'on',
    ]);

    $response->assertRedirect(route('seller.listings.index'));
    $this->assertAuthenticated();
});

it('can login with valid credentials', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password123',
        'terms' => 'on',
    ]);

    $response->assertRedirect(route('seller.listings.index'));
    $this->assertAuthenticatedAs($user);
});

it('throttles login requests after 5 attempts', function () {
    $user = User::factory()->create();

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ])->assertStatus(302);
    }

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(429);
});

it('redirects unverified user to email verification notice page', function () {
    $user = User::factory()->unverified()->create();
    $this->actingAs($user);

    $response = $this->get(route('seller.listings.index'));
    $response->assertRedirect(route('verification.notice'));
});

it('allows verified user to access seller listings dashboard', function () {
    $user = User::factory()->create(); // default is verified
    $this->actingAs($user);

    $response = $this->get(route('seller.listings.index'));
    $response->assertStatus(200);
});

it('verifies user email through signed verification url', function () {
    $user = User::factory()->unverified()->create();
    $this->actingAs($user);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
    );

    $response = $this->get($verificationUrl);
    $response->assertRedirect(route('seller.listings.index'));

    $user->refresh();
    expect($user->hasVerifiedEmail())->toBeTrue();
});

it('can resend verification email', function () {
    $user = User::factory()->unverified()->create();
    $this->actingAs($user);

    $response = $this->post(route('verification.send'));
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Tautan verifikasi baru telah dikirim!');
});
