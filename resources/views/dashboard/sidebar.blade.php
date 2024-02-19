@php
	\Navigator::addItem([
		'text' => 'Files', 'icon_class' => 'fas fa-file-upload', 'resource' => 'manage.files.index'
	], 'default');

	\Navigator::addItem([
		'text' => 'Users', 'icon_class' => 'fas fa-users', 'resource' => 'manage.users.index'
	], 'sidebar.manage');

	\Navigator::addItem([
		'text' => 'API', 'icon_class' => 'fas fa-plug', 'resource' => 'manage.documentation.index'
	], 'sidebar.manage');

	\Navigator::addItem([
		'text' => 'Permissions', 'icon_class' => 'fas fa-user-shield', 'resource' => 'manage.access.index'
	], 'sidebar.manage');

	// \Navigator::addItem([
	// 	'text' => 'API', 'icon_class' => 'fas fa-plug',
	// 	'url' => '/dashboard',				// give a URL or a RESOURCE
	// 	'permission' => 'view-dashboard',	// optional permission
	// 	'order' => 2,						// optional order
	// ], 'sidebar.manage');
@endphp
<ul class="nav">
	<li class="nav-title">Navigation</li>
    <li>
		<a href="{{ route('dashboard') }}">
			<i class="fas fa-tachometer-alt"></i>
			<span>Dashboard</span>
		</a>
	</li>
	@include('oxygen::dashboard.partials.NavBar', ['navBar' => 'default'])

    <li class="nav-title">Admin</li>
	@include('oxygen::dashboard.partials.NavBar', ['navBar' => 'sidebar.manage'])
	<li>
		<a href="{{ route('logout') }}">
			<i class="fas fa-sign-out-alt"></i>
			<span>Logout</span>
		</a>
	</li>
</ul>
