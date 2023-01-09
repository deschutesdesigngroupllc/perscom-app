@extends('app')

@section('pageName', Page::name())

@section('content')
    <h1>{{ Page::title('Default title', 'My website: ', ' • Awesome appended string') }}</h1>
@endsection