@extends('parent')

@section('style')
<link rel="stylesheet" href="/css/master.css" type="text/css" media="screen">
@stop

@section('script')
<script type="text/javascript">
alert('Hello');
</script>
@stop

@section('body')

@include('includes/header')

<h1>Child template extending Parent</h1>
<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>

@include('includes/footer')

@stop

@section('legal')
@parent
<p>This is the child legal bit which should be first</p>
@stop
