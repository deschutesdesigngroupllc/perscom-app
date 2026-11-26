@extends('errors.layout')

@section('title', 'Too Many Requests')
@section('code', '429')
@section('heading', 'Slow Down for a Moment')
@section('message', 'We received too many requests in a short time. Wait a minute, then try again.')
