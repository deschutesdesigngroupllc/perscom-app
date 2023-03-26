@extends('errors.layout')
@section('code', __('401'))
@section('title', __('Bad Request'))
@section('header', __('Bad Request.'))
@if($message = $exception->getMessage())
@section('message', $message)
@else
@section('message', __('Sorry, we are unable to handle that last request.'))
@endif
