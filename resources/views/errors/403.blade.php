@extends('errors.layout')

@section('title', 'Access Denied')
@section('code', '403')
@section('heading', 'You Don\'t Have Access to This Page')
@section('message', filled($exception?->getMessage()) && ! str_contains($exception->getMessage(), 'This action is unauthorized') ? $exception->getMessage() : 'Your role doesn\'t include permission for this page. Ask an administrator in your organization if you need access.')
