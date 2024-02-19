<!DOCTYPE html>
{{--<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">--}}
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">

	<title>@yield('pageTitle', 'Register or Login')</title>

	@vite(['resources/sass/oxygen/bootstrap.scss', 'resources/sass/auth.scss'])
	<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
</head>
<body class="master-auth">
<nav class="navbar navbar-expand-md navbar-light fixed-top navbar-app">
	<div class="container-fluid">
		<a class="navbar-brand" href="{{ url('/') }}">
			{{ config('app.name', 'Laravel') }}
		</a>

		<button class="navbar-toggler" type="button" data-bs-toggle="collapse"
				data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
				aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse justify-content-md-between" id="navbarSupportedContent">
			<!-- Left Side Of Navbar -->
			<ul class="navbar-nav mr-auto"></ul>

			<!-- Right Side Of Navbar -->
			<ul class="navbar-nav ml-auto">
				<!-- Authentication Links -->
				@guest
					<li class="nav-item">
						<a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
					</li>
					<li class="nav-item">
						@if (Route::has('register'))
							<a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
						@endif
					</li>
				@else
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown"
						   role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							{{ Auth::user()->name }} <span class="caret"></span>
						</a>
						<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
							<li>
								<a class="dropdown-item" id="logout" href="{{ route('logout') }}">{{ __('Logout') }}</a>
							</li>
						</ul>
					</li>
				@endguest
			</ul>
		</div>
	</div>
</nav>

<div class="container-fluid h-100">
	<div class="row justify-content-center align-items-center h-100">
		@yield('content')
	</div>
</div>

@vite(['resources/js/public.js'])
@yield('scripts')

</body>
</html>
