<div {{ $attributes->merge(['class' => 'card card-style-data']) }}>
	<div class="card-header text-white bg-secondary">
		<span class="title">{{ $title }}</span>

		@if (isset($headerActions))
			<div class="header-actions">
				{{ $headerActions }}
			</div>
		@endif
	</div>

	<div class="card-body">
		{{ $slot }}
	</div>
</div>
