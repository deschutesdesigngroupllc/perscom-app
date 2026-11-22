@extends('errors.layout')

@section('title', 'Something Broke')
@section('code', '500')
@section('heading', 'Something Broke on Our End')
@section('message',
  'This isn\'t caused by anything you did, and we\'ve been notified. Try again in a moment. If it keeps happening, contact
  support with the request ID below.')
