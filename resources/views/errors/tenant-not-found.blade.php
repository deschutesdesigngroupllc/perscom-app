@extends('errors.layout')
@props(['showLink' => false])
@section('code', __('404'))
@section('title', __('Not Found'))
@section('header', __('Organization not found.'))
@section('message', __('Sorry, we couldn’t find the organization you’re looking for. Please check with your administrator for the proper domain.'))
@section('extra')
    <div class="mt-6">
        <a href="{{ route('web.find-my-organization.index') }}"
           class="text-base font-medium text-blue-600 hover:text-blue-500">Find my organization<span
                aria-hidden="true"> &rarr;</span></a>
    </div>
@endsection
