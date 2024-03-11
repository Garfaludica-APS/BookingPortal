<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * @internal
 *
 * @small
 */
class UpdatePasswordTest extends TestCase
{
	use RefreshDatabase;

	public function testPasswordCanBeUpdated(): void
	{
		$this->actingAs($user = User::factory()->create());

		$response = $this->put('/user/password', [
			'current_password' => 'password',
			'password' => 'new-password',
			'password_confirmation' => 'new-password',
		]);

		static::assertTrue(Hash::check('new-password', $user->fresh()->password));
	}

	public function testCurrentPasswordMustBeCorrect(): void
	{
		$this->actingAs($user = User::factory()->create());

		$response = $this->put('/user/password', [
			'current_password' => 'wrong-password',
			'password' => 'new-password',
			'password_confirmation' => 'new-password',
		]);

		$response->assertSessionHasErrors();

		static::assertTrue(Hash::check('password', $user->fresh()->password));
	}

	public function testNewPasswordsMustMatch(): void
	{
		$this->actingAs($user = User::factory()->create());

		$response = $this->put('/user/password', [
			'current_password' => 'password',
			'password' => 'new-password',
			'password_confirmation' => 'wrong-password',
		]);

		$response->assertSessionHasErrors();

		static::assertTrue(Hash::check('password', $user->fresh()->password));
	}
}
