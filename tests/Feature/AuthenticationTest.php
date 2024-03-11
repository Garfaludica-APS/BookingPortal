<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace Tests\Feature;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @small
 */
class AuthenticationTest extends TestCase
{
	use RefreshDatabase;

	public function testLoginScreenCanBeRendered(): void
	{
		$response = $this->get('/login');

		$response->assertStatus(200);
	}

	public function testUsersCanAuthenticateUsingTheLoginScreen(): void
	{
		$user = User::factory()->create();

		$response = $this->post('/login', [
			'email' => $user->email,
			'password' => 'password',
		]);

		$this->assertAuthenticated();
		$response->assertRedirect(RouteServiceProvider::HOME);
	}

	public function testUsersCanNotAuthenticateWithInvalidPassword(): void
	{
		$user = User::factory()->create();

		$this->post('/login', [
			'email' => $user->email,
			'password' => 'wrong-password',
		]);

		$this->assertGuest();
	}
}
