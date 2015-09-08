<html>
<head>
	@yield('style')
	<title>@yield('title')</title>
</head>
<body>
	@yield('body')
	@yield('script')
	@section('legal')
	<p>This is the parent legal section</p>
	@show
</body>
</html>