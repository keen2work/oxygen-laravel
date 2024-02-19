<?php

namespace App\Http\Controllers\Manage;

use EMedia\Devices\Entities\Devices\Device;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{

	public function dashboard()
	{
		$data = [
			'appName' => config('app.name'),
			'pageTitle' => config('app.name') . ' Dashboard',
		];

		$metrics = new Collection();
		$metrics->push([
			'title' => 'Total Users',
			'count' => app('oxygen')::makeUserModel()::count(),
			'description' => 'Current registered users',
			'route' => 'manage.users.index',
		]);

		$metrics->push([
			'title' => 'Total Devices',
			'count' => Device::count(),
			'description' => 'Current registered devices',
			'route' => 'manage.devices.index',
		]);

		$data['metrics'] = $metrics;

		return view('oxygen::dashboard.dashboard', $data);
	}
}
