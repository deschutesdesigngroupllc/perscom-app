@extends('errors.500')
@if($message = $exception->getMessage())
@section('message', $message)
@endif
