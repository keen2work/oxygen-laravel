@foreach (\Navigator::getNavBar($navBar ?? null)->items() as $item)
	@if ($item->userAllowedToSee())
		<li>
			@if ($item->hasUrl())
				<a href="{{ $item->getUrl() }}">
					@endif
					@if ($item->hasIcon())
						<i class="{{ $item->icon_class }}"></i>
					@endif
					<span>{{ $item->text }}</span>
					@if ($item->hasUrl())
				</a>
			@endif
		</li>
	@endif
@endforeach
