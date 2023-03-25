@extends('errors.layout')
@section('code', __('403'))
@section('title', __('Forbidden'))
@section('header', __('Forbidden.'))
@if($message = $exception->getMessage())
@section('message', $message)
@else
@section('message', __('Sorry, the page you are trying to access is off limits.'))
@endif
