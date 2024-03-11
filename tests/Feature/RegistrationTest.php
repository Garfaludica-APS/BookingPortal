<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace Tests\Feature;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;
use Tests\TestCase;

/**
 * @internal
 *
 * @small
 */
class RegistrationTest extends TestCase
{
	use RefreshDatabase;

	public function testRegistrationScreenCanBeRendered(): void
	{
		if (!Features::enabled(Features::registration()))
			static::markTestSkipped('Registration support is not enabled.');

		$response = $this->get('/register');

		$response->assertStatus(200);
	}

	public function testRegistrationScreenCannotBeRenderedIfSupportIsDisabled(): void
	{
		if (Features::enabled(Features::registration()))
			static::markTestSkipped('Registration support is enabled.');

		$response = $this->get('/register');

		$response->assertStatus(404);
	}

	public function testNewUsersCanRegister(): void
	{
		if (!Features::enabled(Features::registration()))
			static::markTestSkipped('Registration support is not enabled.');

		$response = $this->post('/register', [
			'name' => 'Test User',
			'email' => 'test@example.com',
			'password' => 'password',
			'password_confirmation' => 'password',
			'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature(),
		]);

		$this->assertAuthenticated();
		$response->assertRedirect(RouteServiceProvider::HOME);
	}
}
