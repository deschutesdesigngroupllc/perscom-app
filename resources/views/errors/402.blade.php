@extends('errors.layout')
@section('code', __('402'))
@section('title', __('Subscription Required'))
@section('header', __('Subscription Required.'))
@if($message = $exception->getMessage())
@section('message', $message)
@else
@section('message', __('The account requires a subscription to continue. Please contact your account administrator.'))
@endif
