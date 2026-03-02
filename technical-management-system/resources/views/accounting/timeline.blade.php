@extends('layouts.dashboard')
@section('title', 'Accounting Timeline')
@section('page-title', 'Accounting Timeline')
@section('page-subtitle', 'Track new requests, job orders, and ongoing progress')

@section('sidebar-nav')
    @include('accounting.partials.sidebar')
@endsection

@section('content')
    <x-activity-timeline role="accounting" :stats="$stats" :timeline-entries="$timelines" :filters="$filters" />
@endsection
