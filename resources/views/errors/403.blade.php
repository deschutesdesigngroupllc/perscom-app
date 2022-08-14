@extends('errors.layout')
@section('title', __('Forbidden'))
@section('content')
    <div class="min-h-full pt-16 pb-12 flex flex-col bg-white">
        <main class="flex-grow flex flex-col justify-center max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex-shrink-0 flex justify-center">
                <a href="/" class="inline-flex">
                    <img class="h-12 w-auto" src="{{ asset('svg/app-logo.svg') }}" alt="">
                </a>
            </div>
            <div class="py-16">
                <div class="text-center">
                    <p class="text-sm font-semibold text-blue-600 uppercase tracking-wide">403 error</p>
                    <h1 class="mt-2 text-4xl font-extrabold text-gray-900 tracking-tight sm:text-7xl">Forbidden.</h1>
                    <p class="mt-2 text-base text-gray-500">Sorry, the area you are trying to access is forbidden or does not exist.</p>
                    <div class="mt-6">
                        <a href="#" class="text-base font-medium text-blue-600 hover:text-blue-500">Go back home<span aria-hidden="true"> &rarr;</span></a>
                    </div>
                </div>
            </div>
        </main>
        <footer class="flex-shrink-0 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex justify-center space-x-4">
                <a href="#" class="text-sm font-medium text-gray-500 hover:text-gray-600">Contact Support</a>
                <span class="inline-block border-l border-gray-300" aria-hidden="true"></span>
                <a href="#" class="text-sm font-medium text-gray-500 hover:text-gray-600">Status</a>
                <span class="inline-block border-l border-gray-300" aria-hidden="true"></span>
                <a href="#" class="text-sm font-medium text-gray-500 hover:text-gray-600">Twitter</a>
            </nav>
        </footer>
    </div>
    @endsectio
