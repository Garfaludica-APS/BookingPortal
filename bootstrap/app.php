<?php

declare(strict_types=1);

/*
 * Copyright © 2024 - Garfaludica APS - MIT License
 */

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\LocalizeApp;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

return Application::configure(basePath: \dirname(__DIR__))
	->withRouting(
		web: __DIR__ . '/../routes/web.php',
		commands: __DIR__ . '/../routes/console.php',
		health: '/up',
	)
	->withMiddleware(static function(Middleware $middleware): void {
		$middleware->encryptCookies(except: [
			'lang',
		]);
		$middleware->alias([
			'lang' => LocalizeApp::class,
		]);
		$middleware->web(append: [
			HandleInertiaRequests::class,
		]);
		$middleware->redirectGuestsTo(
			fn (Request $request) => App::isLocale('en') ? route('en.auth.login') : route('auth.login')
		);
		$middleware->redirectUsersTo(
			fn (Request $request) => route('admin.dashboard')
		);
	})
	->withExceptions(static function(Exceptions $exceptions): void {
		$exceptions->respond(function (Response|RedirectResponse $response, Throwable $exception, Request $request): Response|RedirectResponse {
			if (app()->isProduction() && in_array($response->status(), [500, 503, 404, 403]))
				return inertia('Error', ['status' => $response->status()])
					->toResponse($request)
					->setStatusCode($response->status());
			if ($response->status() === 419)
				return back()->with([
					'message' => __('The page expired, please try again.'),
				]);
			return $response;
		});
	})->create();
