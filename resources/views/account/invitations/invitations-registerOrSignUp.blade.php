@extends('oxygen::layouts.master-auth')

@section('content')
    <div class="container">
        <div class="row justify-content-center">

            @include('oxygen::partials.flash')

            <div class="col-md-8">

                <div class="accordion" id="accordionLogin">

					<div class="accordion-item">
						<h2 class="accordion-header" id="headingTwo">
							<button class="accordion-button @if (!$plausibleUser) @else collapsed @endif " type="button"
									data-bs-toggle="collapse" data-bs-target="#collapseTwo"
									aria-expanded="@if (!$plausibleUser) 'true' @else 'false' @endif"
									aria-controls="collapseTwo">
								Register and Join the Team
							</button>
						</h2>
						<div id="collapseTwo" class="accordion-collapse collapse @if (!$plausibleUser) show @endif"
							 aria-labelledby="headingTwo" data-bs-parent="#accordionLogin">
							<div class="accordion-body">
								<form class="form-horizontal" role="form" method="POST" action="{{ route('register.store') }}">
									@csrf
									<input type="hidden" name="invitation_code" value="{{ $invite->invitation_code }}">

									<div class="form-group row text-center">
										<div class="col-md-12">
											<h3>You've been invited to join a team.</h3>

											<div class="copy">
												<p>Complete registration and see what's inside.</p>
											</div>
										</div>
									</div>

									@include('oxygen::auth.register_form_fields')

									<div class="form-group row mb-0">
										<div class="col-md-6 offset-md-4">
											<button type="submit" class="btn btn-primary">
												Accept the Invitation
											</button>
										</div>
									</div>

									<hr/>
									<div class="form-group">
										<div class="col-md-8 offset-md-4">
											Already have an account?
											<a href="#collapseThree" data-bs-parent="#accordionLogin" data-bs-toggle="collapse" data-bs-target="#collapseThree">Login</a>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
					<div class="accordion-item">
						<h2 class="accordion-header" id="headingThree">
							<button class="accordion-button @if (!$plausibleUser) collapsed @endif" type="button"
								data-bs-toggle="collapse"
								data-bs-target="#collapseThree"
								aria-expanded="@if ($plausibleUser) 'true' @else 'false' @endif"
								aria-controls="collapseThree">
								Already have an account? Login Here
							</button>
						</h2>
						<div id="collapseThree" class="accordion-collapse collapse @if ($plausibleUser) show @endif" aria-labelledby="headingThree" data-bs-parent="#accordionLogin">
							<div class="accordion-body">
								<form class="form-horizontal" role="form" method="POST" action="/login">
									@csrf
									<input type="hidden" name="invitation_code" value="{{ $invite->invitation_code }}">

									@include('oxygen::auth.login_form_fields')

									<hr/>

									<div class="form-group">
										<div class="col-md-8 offset-md-4">
											Don't have an account?
											<a href="#collapseTwo" data-bs-parent="#accordion" data-bs-toggle="collapse" data-bs-target="#collapseTwo">Signup for a New Account</a>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>

                </div>
            </div>
        </div>
    </div>
@endsection
