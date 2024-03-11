<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
	/**
	 * The URIs that should be reachable while maintenance mode is enabled.
	 *
	 * @var array<int, string>
	 */
	protected $except = [
	];
}
