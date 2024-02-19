<?php

namespace EMedia\Oxygen\Http\Middleware;

use Closure;
use EMedia\MultiTenant\Facades\TenantManager;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Facades\View;

class LoadViewSettings
{
	/**
	 * The Guard implementation.
	 *
	 * @var Guard
	 */
	protected $auth;

	/**
	 * Create a new filter instance.
	 *
	 * @param  Guard  $auth
	 * @return void
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$appName 	= config('app.name');
		$pageTitle	= config('oxygen.dashboard.default_page_title');

		$view = view();

		$view->share('appName', $appName);
		$view->share('pageTitle', $pageTitle);

		if ($user = $this->auth->user()) {
			$view->share('user', $user);

			// DONE: handle multiple tenants and save in session
			// TODO: MUST check acceptInvite() in InvitationsController
			// if (TenantManager::multiTenancyIsActive() && TenantManager::isTenantNotSet())
			// 	TenantManager::setTenant($user->tenants()->first());

			// Tenants
			// $tenants = TenantManager::allTenants();
			if (TenantManager::multiTenancyIsActive()) {
				if (TenantManager::isTenantSet()) {
					$tenant = TenantManager::getTenant();
					$view->share('tenant', $tenant);
				}
				$view->share('tenants', $user->tenants);
			}
		}

		return $next($request);
	}
}
