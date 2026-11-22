@extends('errors.layout')

@php
  $exceptionMessage = (string) $exception?->getMessage();
  $linkExpired = $exceptionMessage === 'Invalid signature.';
  $heading = $linkExpired ? 'This Link Has Expired' : 'You Don\'t Have Access to This Page';
  $message = match (true) {
      $linkExpired
          => 'Links in our emails stop working after a while for your security. Start again and we\'ll email you a fresh one. Signing up again with the same email is fine.',
      filled($exceptionMessage) && !str_contains($exceptionMessage, 'This action is unauthorized') => $exceptionMessage,
      default => 'Your role doesn\'t include permission for this page. Ask an administrator in your organization if you need access.',
  };
@endphp

@section('title', $linkExpired ? 'Link Expired' : 'Access Denied')
@section('code', '403')
@section('heading', $heading)
@section('message', $message)
