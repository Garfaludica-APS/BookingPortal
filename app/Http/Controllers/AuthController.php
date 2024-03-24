<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\RateLimiters\LoginRateLimiter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class AuthController extends Controller
{
	protected LoginRateLimiter $limiter;

	public function __construct(LoginRateLimiter $limiter)
	{
		$this->limiter = $limiter;
	}

	public function authenticate(LoginRequest $request): RedirectResponse
	{
		$credentials = $request->credentials();

		if (Auth::attempt($credentials, $request->boolean('remember'))) {
			$request->session()->regenerate();
			$this->limiter->clear($request);
			return redirect()->intended(route('admin.dashboard'));
		}

		if ($this->limiter->tooManyAttempts($request)) {
			$seconds = $this->limiter->availableIn($request);
			return redirect()->back()->withErrors([
				'login' => __(
					'Too many login attempts. Please try again in :seconds seconds.',
					['seconds' => $seconds]
				),
			])->onlyInput('username', 'remember');
		}

		$this->limiter->increment($request);

		return redirect()->back()->withErrors([
			'login' => __('These credentials do not match our records.'),
		])->onlyInput('username', 'remember');
	}

	public function login(Request $request): Response|RedirectResponse
	{
		return inertia('Auth/Login');
	}

	public function logout(Request $request): RedirectResponse
	{
		Auth::logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();
		$prefix = App::isLocale('it') ? '' : App::currentLocale() . '.';
		return to_route("{$prefix}auth.login");
	}
}
