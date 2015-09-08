<html>
<head>
	<title>@yield('title')</title>
</head>
<body>
	<h1>{{ $title }}</h1>

	<div class="posts">
		@foreach($posts as $post)
		<div class="post">
			
		</div>
		@endforeach
	</div>

	@set('choice', 'Dark side')
	
	{{ $choice }}
	
	@lang('something.to.translate')
	@choice('something.to.translate', $choice)

	@unset($choice)

	<div class="posts">
		@forelse ($posts as $post)
		<div class="post">
			
		</div>
		@empty
		<p>There are no posts</p>
		@endforelse
	</div>
	
	@if ($night == $day)
	<p>Welcome dark soul</p>
	@elseif ($day == $night)
	<p>So you arrived</p>
	@else
	<p>Yo!</p>
	@endif
	
	
</body>
</html>