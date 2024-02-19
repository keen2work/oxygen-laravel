<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="">
	<meta name="ROBOTS" content="NOINDEX, NOFOLLOW">

	<title>@yield('pageTitle', $pageTitle)</title>
	@vite(['resources/sass/oxygen/bootstrap.scss', 'resources/sass/backend.scss'])
	<link rel="shortcut icon" href="/favicon.ico"/>
	@stack('stylesheets')
	@stack('meta')
	@include('oxygen::partials.tracking')
</head>
<body>

<?php if (preg_match('/sandbox|preview/i', request()->getHttpHost())) { ?>
<div class="site-wide-message">Sandbox Mode - For Demo Only</div>
<?php } ?>

<div id="dashboard-wrapper">

	<div id="dashboard-home">

		<div class="account-header">
			{{--<div class="container-fluid">--}}

			<nav class="navbar navbar-expand-md navbar-dark navbar-app">
				<div class="container-fluid">

					<div class="left">
					<span class="btn btn-header-menu js-toggle-right-sidebar">
                		<i class="fas fa-bars js-icon"></i>
					</span>
						<span class="btn btn-header-menu js-toggle-right-mini-sidebar">
                		<i class="fas fa-arrow-left js-icon"></i>
					</span>

						<a class="navbar-brand" href="{{ url('/') }}">
							{{ config('app.name', 'Laravel') }}
						</a>
					</div>

					<button class="navbar-toggler" type="button" data-bs-toggle="collapse"
							data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
							aria-expanded="false" aria-label="Toggle navigation">
						<i class="fas fa-bars"></i>
					</button>

					<div class="collapse navbar-collapse justify-content-md-between" id="navbarSupportedContent">
						{{-- Left side of navbar --}}
						<ul class="navbar-nav mr-auto"></ul>

						{{-- Right side of navbar --}}
						<ul class="navbar-nav ml-auto d-md-flex">
							{{-- Authentication links --}}

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
									   role="button" data-bs-toggle="dropdown" aria-expanded="false">
										{{ Auth::user()->name }}
									</a>
									<ul class="dropdown-menu" aria-labelledby="navbarDropdown">
										<li>
											<a class="dropdown-item" href="{{ route('account.profile') }}">
												{{ __('My Profile') }}
											</a>
										</li>
										<li>
											<hr class="dropdown-divider">
										</li>
										<li><a class="dropdown-item" href="{{ route('account.email') }}">Edit Email</a>
										</li>
										<li><a class="dropdown-item" href="{{ route('account.password') }}">
												Edit Password</a></li>
										<li>
											<hr class="dropdown-divider">
										</li>
										<li>
											<a class="dropdown-item" id="logout" href="{{ route('logout') }}">
												{{ __('Logout') }}
											</a>
										</li>
									</ul>
								</li>
							@endguest
						</ul>
					</div>
				</div>
			</nav>
		</div>

		@yield('page-container')

		@include('oxygen::partials.footer')
	</div>
</div>

@vite(['resources/js/backend.js'])
@stack('js')
@stack('scripts')

</body>
</html>
