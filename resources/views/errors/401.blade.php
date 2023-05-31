@extends('errors.layout')
@section('code', __('401'))
@section('title', __('Unauthorized'))
@section('header', __('Unauthorized.'))
@if($message = $exception->getMessage())
@section('message', $message)
@else
@section('message', __('Please try logging in to access this page.'))
@endif
