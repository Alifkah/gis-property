<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

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
