<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

namespace App\Providers;

use App\Lang\Manager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class LangManagerServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 */
	public function register(): void
	{
		$this->app->singleton(Manager::class, static fn(Application $app) => new Manager(Storage::disk('translations')));
	}

	/**
	 * Bootstrap services.
	 */
	public function boot(): void {}

	public function provides(): array
	{
		return [Manager::class];
	}
}
