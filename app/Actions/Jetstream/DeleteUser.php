<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Actions\Jetstream;

use App\Models\User;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
	/**
	 * Delete the given user.
	 */
	public function delete(User $user): void
	{
		$user->deleteProfilePhoto();
		$user->tokens->each->delete();
		$user->delete();
	}
}
