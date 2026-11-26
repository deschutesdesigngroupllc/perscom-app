@extends('errors.layout')

@section('title', 'Session Expired')
@section('code', '419')
@section('heading', 'Your Session Expired')
@section('message', 'For your security, the page timed out. Go back, refresh the page and try again.')
@section('action')
  <a class="button" href="{{ url()->previous() }}">Go Back</a>
@endsection
