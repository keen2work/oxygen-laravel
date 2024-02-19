@extends('oxygen::layouts.master-auth')

@section('content')
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-8">
				<div class="card">
					<div class="card-header">{{ __('Verify Your Email Address') }}</div>

					<div class="card-body pb-4 pt-4">
						@if (session('resent'))
							<div class="alert alert-success" role="alert">
								{{ __('A fresh verification link has been sent to your email address.') }}
							</div>
						@endif

						{{ __('Before proceeding, please check your email for a verification link. Please wait for a few minutes and check your spam folders.') }}
						<br><br>
						<form action="{{ route('verification.send') }}" method="post">
							@csrf
							{{ __('If you did not receive the email, request a new email.') }} <br><br>
							<button type="submit" class="btn btn-outline-info">{{ __('Resend Verification Email') }}</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
