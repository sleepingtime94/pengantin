@extends('layouts.app')

@section('title', 'DASHBOARD - PENGANTIN')

@section('content')

@if (!empty($users) && isset($users['user_step']))
@if ($users['user_step'] == 1)
@include('dashboard.formulir')
@elseif ($users['user_step'] == 2)
@include('dashboard.upload')
@elseif ($users['user_step'] == 3)
@include('dashboard.timeline')
@endif
@endif

@endsection