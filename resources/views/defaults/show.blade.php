@extends('oxygen::layouts.master-dashboard')

@section('breadcrumbs')
	{{ lotus()->breadcrumbs([
		['Dashboard', route('dashboard')],
		// ['Change The Resource Name', route('<change here>')],
		[$pageTitle, null, true]
	]) }}
@stop

@section ('content')
	{{ lotus()->pageHeadline($pageTitle) }}

	<div class="page-main-actions">
		@yield('breadcrumbs')
	</div>

	<x-oxygen::data.card :title="$pageTitle" class="mt-4">
		<x-oxygen::data.row label="Name">{{ $entity->name }}</x-oxygen::data.row>
		<x-oxygen::data.row label="Created">{{ standard_datetime($entity->created_at) }}</x-oxygen::data.row>
	</x-oxygen::data.card>

	<x-oxygen::data.card :title="$pageTitle" class="mt-4">
		<x-slot name="headerActions">
			<a href="#"
			   class="btn btn-sm btn-outline-light">Header Button</a>
		</x-slot>
		<x-oxygen::data.row label="Name">{{ $entity->name }}</x-oxygen::data.row>
		<x-oxygen::data.row label="Created">{{ standard_datetime($entity->created_at) }}</x-oxygen::data.row>
		<x-oxygen::data.row label="Related Items">
			<ul class="list-group">
				@foreach (['One', 'Two', 'Three'] as $itemName)
					<li class="list-group-item">{{ $itemName }}</li>
				@endforeach
			</ul>
		</x-oxygen::data.row>
	</x-oxygen::data.card>

	<div class="card mt-4">
		<div class="card-header">
			Sample Title
		</div>
		<div class="card-body">
			<div>Sample Content</div>
		</div>
	</div>
@stop
