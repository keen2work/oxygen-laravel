@if (Session::has('alert'))
    <div class="container alert-container">
        <div class="alert alert-info alert-dismissible fade show">
            <span>{{ Session::get('alert') }}</span>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if (Session::has('success'))
    <div class="container alert-container">
        <div class="alert alert-success alert-dismissible fade show">
            <span><strong>Success</strong> {{ Session::get('success') }}</span>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if (Session::has('status'))
    <div class="container alert-container">
        <div class="alert alert-success alert-dismissible fade show">
            <span>{{ Session::get('status') }} </span>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if (Session::has('error'))
    <div class="container alert-container">
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Whoops!</strong> There were some problems.<br>
            @if (is_array(Session::get('error')))
                <span>{{ implode(' ', Session::get('error')) }}</span>
            @else
                <span>{{ Session::get('error') }}</span>
            @endif
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif

@if (isset($errors) && is_countable($errors) && count($errors) > 0)
    <div class="container alert-container">
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
@endif
