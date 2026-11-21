@extends('errors.layout')

@section('title', 'Temporarily Unavailable')
@section('code', '503')
@section('heading', 'PERSCOM Is Briefly Unavailable')
@section('message', filled($exception?->getMessage()) ? $exception->getMessage() : 'We\'re doing maintenance or setting things up. Check back in a few minutes.')
