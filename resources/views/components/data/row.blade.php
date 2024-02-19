<div {{ $attributes->merge(['class' => 'data-view-group row']) }}>
	<div class="col col-sm-4">{{ $label }}</div>
	<div class="col col-sm-8">
		{{ $slot }}
	</div>
</div>
