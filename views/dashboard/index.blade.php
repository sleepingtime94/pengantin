@extends('layouts.app')

@section('title', 'DASHBOARD - PENGANTIN')

@section('content')
@if ($users['user_step'] == 1)
@include('dashboard.formulir')
@elseif ($users['user_step'] == 2)
@include('dashboard.upload')
@elseif ($users['user_step'] == 3)
@include('dashboard.timeline')
@endif
@endsection
