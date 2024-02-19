@extends('oxygen::layouts.master-auth')

@php
	$email = old('email') ?? '';
	if (request()->has('email')) {
		$email = request()->get('email');
	}
@endphp

@section('content')
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-8">
				<div class="card">
					<div class="card-header">{{ __('Reset Password') }}</div>

					<div class="card-body pb-4 pt-4">
						<form method="POST" action="{{ route('password.update') }}" class="form form-horizontal">
							@csrf

							<input type="hidden" name="token" value="{{ request()->route('token') }}">

							<div class="form-group row">
								<label for="email" class="col-md-4 col-form-label text-md-end">{{ __('E-Mail Address') }}</label>

								<div class="col-md-6">
									<input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email }}" required autofocus>

									@if ($errors->has('email'))
										<span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group row">
								<label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

								<div class="col-md-6">
									<input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

									@if ($errors->has('password'))
										<span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
									@endif
								</div>
							</div>

							<div class="form-group row">
								<label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

								<div class="col-md-6">
									<input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
								</div>
							</div>

							<div class="form-group row mb-0">
								<div class="col-md-6 offset-md-4">
									<button id="reset-button" type="submit" class="btn btn-primary">
										{{ __('Reset Password') }}
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
